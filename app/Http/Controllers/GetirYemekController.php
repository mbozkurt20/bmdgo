<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use App\Models\CourierOrder;
use App\Models\Restaurant;
use App\Models\Order;
use App\Models\Admin;

class GetirYemekController extends Controller
{
    public function index()
    {
        $restaurants = Restaurant::where('getir_restaurant_secret_key', '!=', '')->get();

        foreach ($restaurants as $restaurant) {

            if ($restaurant->getir_token == null || $restaurant->getir_restaurant_id == "") {
                $loginData = $this->login($restaurant->getir_app_secret_key, $restaurant->getir_restaurant_secret_key, $restaurant->id);
            } else {
                $loginData = [
                    "getir_token" => $restaurant->getir_token,
                    "getir_restaurant_id" => $restaurant->getir_restaurant_id,
                ];
            }

            $this->orders($loginData["getir_token"], $loginData["getir_restaurant_id"], $restaurant->id);
        }
    }

    private function login($getir_app_secret_key, $getir_restaurant_secret_key, $restaurant_id)
    {
        $url = 'https://food-external-api-gateway.development.getirapi.com/auth/login';
 //production: $url = 'https://food-external-api-gateway.getirapi.com/auth/login';

        $data = [
            "appSecretKey" => $getir_app_secret_key,
            "restaurantSecretKey" => $getir_restaurant_secret_key
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $result = curl_exec($ch);
        curl_close($ch);

        $resultd = json_decode($result);

        if (!isset($resultd->token) || !isset($resultd->restaurantId)) {
            return [
                'getir_token' => null,
                'getir_restaurant_id' => null
            ];
        }

        $loginData = [
            "getir_token" => $resultd->token,
            "getir_restaurant_id" => $resultd->restaurantId
        ];

        Restaurant::where('id', $restaurant_id)->update($loginData);

        return $loginData;
    }

    private function orders($token, $restaurant_id, $k_restaurant_id)
    {
        if (!$token) return;

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://food-external-api-gateway.development.getirapi.com/food-orders/active',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'token: ' . $token
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        $getorder = json_decode($response);

        if (isset($getorder->code) && $getorder->code == 7) {
            Restaurant::where('id', $k_restaurant_id)->update([
                "getir_token" => null,
                "getir_restaurant_id" => null
            ]);
            return;
        }

        if (!is_array($getorder)) return;

        foreach ($getorder as $row) {
            $info = isset($row->client) ? $row->client : (object)[
                'deliveryAddress' => (object)[
                    'address' => 'Bilinmiyor',
                    'description' => ''
                ],
                'clientPhoneNumber' => 'Yok',
                'name' => 'Bilinmiyor'
            ];

            $address = (object)$info->deliveryAddress;
            $payment = isset($row->paymentMethodText) ? $row->paymentMethodText : (object)['tr' => 'Bilinmiyor'];
            $products = $row->products ?? [];

            $amount = $row->totalDiscountedPrice ?? $row->totalPrice ?? 0;

            $payment_tr = $payment->tr == "Online Ödeme" ? "Online Kredi/Banka Kartı" : $payment->tr;

            $orderData = [
                'platform' => 'getir',
                'courier_id' => -1,
                'status' => 'PENDING',
                'restaurant_id' => $k_restaurant_id,
                'tracking_id' => $row->id,
                'full_name' => $info->name,
                'phone' => $info->clientPhoneNumber,
                'amount' => $amount,
                'payment_method' => $payment_tr,
                'items' => json_encode($products),
                'address' => $address->address ?? 'Yok',
                'notes' => $address->description ?? '',
                'confirmationId' => $row->confirmationId ?? null
            ];

            $order = Order::where('tracking_id', $row->id)->first();

            if (!$order) {
                $order = Order::create($orderData);
            }

            $auto = Admin::find(1);
            if ($auto && $auto->auto_orders == 1) {
                $courie = Courier::where('restaurant_id', 0)->where('situation', 'Aktif')->first();
                if ($courie) {
                    CourierOrder::create([
                        'courier_id' => $courie->id,
                        'order_id' => $order->id
                    ]);

                    $courie->situation = "Serviste";
                    $courie->save();
                }
            }
        }
    }
}
