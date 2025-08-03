<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\Order;
use App\Models\Admin;
use App\Models\Courier;
use App\Jobs\AssignOrderToCourier;
use Illuminate\Support\Facades\Log;

class TrendyolYemekController extends Controller
{
    function remove_emoji($string)
    {
        $regex_sets = [
            '/[\x{1F600}-\x{1F64F}]/u', // Emoticons
            '/[\x{1F300}-\x{1F5FF}]/u', // Symbols & Pictographs
            '/[\x{1F680}-\x{1F6FF}]/u', // Transport & Map
            '/[\x{2600}-\x{26FF}]/u',   // Misc Symbols
            '/[\x{2700}-\x{27BF}]/u'    // Dingbats
        ];

        foreach ($regex_sets as $regex) {
            $string = preg_replace($regex, '', $string);
        }

        return $string;
    }

    public function index()
    {
        $restaurants = Restaurant::get();

        foreach ($restaurants as $restaurant) {
            $this->orders($restaurant);
        }
    }

    private function orders($restaurant)
    {
        $url = 'https://api.trendyol.com/mealgw/suppliers/' . $restaurant->trendyol_satici_id . '/packages';
        $header = array(
            'Authorization: Basic ' . base64_encode($restaurant->trendyol_api_key . ":" . $restaurant->trendyol_secret_key),
            'x-agentname: application/json',
            'x-executor-user: application/json',
            'Content-Type: application/json',
            'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1)'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $result = curl_exec($ch);
        $content = json_decode($result);

        if (!isset($content->content)) {
            return;
        }

        foreach ($content->content as $row) {

            Log::info(json_encode($row));

            $order = Order::where('tracking_id', $row->orderId)->first();
            if ($order) {
                continue;
            }

            $address = $row->address;

            $orderAddress = $address->city . " " . $address->district . " " . $address->address1 . " Kat: " . $address->floor . " Kapi no:" . $address->doorNumber . " Adres Detay:" . $address->addressDescription;

			$promotionsAmount = 0;
            if (isset($row->promotions) && is_array($row->promotions)) {
                foreach ($row->promotions as $promotion) {
                    $promotionsAmount += (float) $promotion->totalSellerAmount;
                }
            }

            $couponAmount = 0;
            if (isset($row->coupon)) {
                $couponAmount += (float) $row->coupon->totalSellerAmount;
            }

            $orderData = [
                'platform'       => 'trendyol',
                'courier_id'     => 0,
                'status'         => 'PENDING',
                'restaurant_id'  => $restaurant->id,
                'tracking_id'    => $row->orderId,
                'full_name'      => $address->firstName . " " . $address->lastName,
                'phone'          => $address->phone . '/' . substr($row->orderId, -11 ,11),
                'payment_method' => $row->payment->paymentType,
                'items'          => json_encode($row->lines),
                'address'        => $orderAddress,
   				'promotions'     => isset($row->promotions) ? json_encode($row->promotions) : [],
                'coupon'         => isset($row->coupon) ? json_encode($row->coupon) : [],
                'sub_amount'     => $row->totalPrice,
                'amount'         => (float) $row->totalPrice - $couponAmount - $promotionsAmount,
                'notes'          => $row->customerNote
            ];

            $order = Order::create($orderData);
            AssignOrderToCourier::dispatch($order);
        }
    }

    public function orderStatus(Request $request)
    {
        $action = $request->action;

        $order = Order::where('tracking_id', $request->tracking_id)->first();
        if (!$order) {
            return response()->json(['message' => 'Sipariş bulunamadı'], 404);
        }

        $order->status = $action;
        $order->save();

        $restaurant = Restaurant::find($order->restaurant_id);
        if (!$restaurant) {
            return response()->json(['message' => 'Restoran bulunamadı'], 404);
        }

        switch ($action) {
            case 'picked':
            case 'manual-shipped':
            case 'manual-delivered':
            case 'unsupplied':
                $type = $action;
                break;
            default:
                return response()->json(['message' => 'Geçersiz işlem'], 400);
        }

        $url = 'https://api.trendyol.com/mealgw/suppliers/' . $restaurant->trendyol_satici_id . '/packages/' . $type;

        $header = array(
            'Authorization: Basic ' . base64_encode($restaurant->trendyol_api_key . ":" . $restaurant->trendyol_secret_key),
            'x-agentname: application/json',
            'x-executor-user: application/json',
            'Content-Type: application/json',
            'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1)'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $result = curl_exec($ch);
        $content = json_decode($result);

        return response()->json(['message' => 'Sipariş durumu güncellendi'], 200);
    }
}
