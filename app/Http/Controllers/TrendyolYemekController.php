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


    function remove_emoji($string) {

        // Match Emoticons
        $regex_emoticons = '/[\x{1F600}-\x{1F64F}]/u';
        $clear_string = preg_replace($regex_emoticons, '', $string);

        // Match Miscellaneous Symbols and Pictographs
        $regex_symbols = '/[\x{1F300}-\x{1F5FF}]/u';
        $clear_string = preg_replace($regex_symbols, '', $clear_string);

        // Match Transport And Map Symbols
        $regex_transport = '/[\x{1F680}-\x{1F6FF}]/u';
        $clear_string = preg_replace($regex_transport, '', $clear_string);

        // Match Miscellaneous Symbols
        $regex_misc = '/[\x{2600}-\x{26FF}]/u';
        $clear_string = preg_replace($regex_misc, '', $clear_string);

        // Match Dingbats
        $regex_dingbats = '/[\x{2700}-\x{27BF}]/u';
        $clear_string = preg_replace($regex_dingbats, '', $clear_string);

        return $clear_string;
    }

    public function index()
    {
        $restaurants = Restaurant::get();

        foreach ($restaurants as $restaurant) {
            $this->orders($restaurant);
        }
    }

    //Siparişler
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

            $notes = $this->remove_emoji($row->customerNote);

            $orderData = [
                'platform'       => 'trendyol',
                'courier_id'     => 0,
                'status'       => 'PENDING',
                'restaurant_id'  => $restaurant->id,
                'tracking_id'    => $row->orderId,
                'full_name'      => $address->firstName . " " . $address->lastName,
                'phone'          => $address->phone . '/' . substr($row->orderId, -10 ,10),
                'amount'         => $row->totalPrice,
                'payment_method' => 'Online Kredi/Banka Kartı', //$row->payment->paymentType
				'payment_method  == "CASH") ' => 'Kapıda Nakit ile Ödeme', //$row->payment->paymentType
				'payment_method  == "CARD") ' => 'Kapıda Kart ile Ödeme', //$row->payment->paymentType
                'items'          => json_encode($row->lines),
                'address'        => $orderAddress,
                //'notes'          => $notes
            ];

            $order = Order::create($orderData); 
            AssignOrderToCourier::dispatch($order);

        }
    }

    //Sipariş değişim 
    /**
     * tracking_id
     * action
     */
    public function orderStatus(Request $request)
    {
        $action = $request->action;

        $order = Order::where('tracking_id', $request->tracking_id)->first();
        if (!$order) {
            return response()->json(['message' => 'Sipariş bulunamadı'], 404);
        }else{
            
            $order->status = $action;
            $order->save();
        } 


        $restaurant = Restaurant::where('id', $order->restaurant_id)->first();
        if (!$restaurant) {
            return response()->json(['message' => 'Restoran bulunamadı'], 404);
        }

        switch ($action) {
            case 'picked':
                $type = 'picked';
                break;
            case 'manual-shipped':
                $type = 'manual-shipped';
                break;
            case 'manual-delivered':
                $type = 'manual-delivered';
                break;
            case 'unsupplied':
                $type = 'unsupplied';
                break;
            default:
                # code...
                break;
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
