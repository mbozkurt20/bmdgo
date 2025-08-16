<?php

namespace App\Http\Controllers;

use App\Helpers\OrdersHelper;
use App\Jobs\AssignPendingOrders;
use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\Order;

class TrendyolYemekController extends Controller
{
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
                'payment_method' => $row->payment->paymentType == 'PAY_WITH_CARD'
                    ? 'Kredi Kart ile Ödeme'
                    : ($row->payment->paymentType == 'PAY_WITH_ON_DELIVERY' ? 'Kapıda Ödeme' : $row->payment->paymentType),
                'items'          => json_encode($row->lines),
                'address'        => $orderAddress,
   				'promotions'     => isset($row->promotions) ? json_encode($row->promotions) : json_encode([]),
                'coupon'         => isset($row->coupon) ? json_encode($row->coupon) : json_encode([]),
                'sub_amount'     => $row->totalPrice,
                'discount'       => $couponAmount+$promotionsAmount,
                'amount'         => (float) $row->totalPrice - $couponAmount - $promotionsAmount,
                'notes'          => $row->customerNote,
                'distance' => OrdersHelper::haversineDistance(
                    $restaurant->latitude,
                    $restaurant->longitude,
                    $row->address->latitude,
                    $row->address->longitude
                )
            ];

            $order = Order::create($orderData);
            AssignPendingOrders::dispatch();
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
        $order->update();

        $restaurant = Restaurant::find($order->restaurant_id);

        if (!$restaurant) {
            return response()->json(['message' => 'Restaurant Bulunamadı'], 404);
        }

        $url = 'https://api.trendyol.com/mealgw/suppliers/' . $restaurant->trendyol_satici_id . '/packages/' . $action;

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
