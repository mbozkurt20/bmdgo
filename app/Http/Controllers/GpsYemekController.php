<?php

namespace App\Http\Controllers;

use App\Helpers\OrdersHelper;
use App\Helpers\OrderStatus;
use App\Models\City;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class GpsYemekController extends Controller
{
    function index()
    {
        $restaurants = Restaurant::all();
        foreach ($restaurants as $restaurant) {
            $this->orders($restaurant);
        }
    }

    private function orders($restaurant)
    {
        $api_token = $restaurant->gpsyemek_api_key;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $api_token
        ])->post('https://gpsyemek.com/api/v1/webhook/orders', [
            'event' => 'get_orders'
        ]);

        $orders = $response->json()['orders'] ?? [];

        foreach ($orders as $row) {
            $order = Order::where('tracking_id', $row['order_code'])->first();
            if ($order) {
                continue;
            }

            $address = json_decode($row['address']);

            $create  = Customer::where('name',  $row['customer']['first_name']." ". $row['customer']['last_name'])->where('restaurant_id',$restaurant->id)->where('phone',$row['customer']['phone'])->first();
            if (!$create) {
                $create = new Customer();
                $create->restaurant_id = $restaurant->id; // Assuming the authenticated user is the restaurant
                $create->name = $row['customer']['first_name'] . " " . $row['customer']['last_name'];
                $create->phone = $row['customer']['phone'];
                $create->mobile = $row['mobile'];
                $create->email = $row['customer']['email'] ?? null;
                $create->save();

                if ($create) {
                    $addr = new CustomerAddress();
                    $addr->customer_id = $create->id;
                    $addr->restaurant_id = $restaurant->id;
                    $addr->name = $row['customer']['first_name'] . " " . $row['customer']['last_name'] . '-GpsYemek';
                    $addr->sokak_cadde = ' ';
                    $addr->bina_no = $address->apartment;
                    $addr->city_id = ' ';
                    $addr->kat = ' ';
                    $addr->latitude = $row['lat'];
                    $addr->longitude = $row['long'];
                    $addr->daire_no = ' ';
                    $addr->mahalle = ' ';
                    $addr->adres_tarifi = $address->address ?? '';
                    $addr->save();
                }
            }

            $orderData = [
                'platform' => 'gpsyemek',
                'customer_id' => $create->id,
                'restaurant_id' => $restaurant->id,
                'courier_id' => -1,
                'status' => OrderStatus::PENDING,
                'tracking_id' => $row['order_code'],
                'full_name' => $row['customer']['first_name'] . " " .$row['customer']['last_name'],
                'phone' => $row['customer']['first_name'] . '/' . substr($row['order_code'], -11, 11),
                'payment_method' =>$row['payment_method_name'],
                'items' => json_encode($row['items']),
                'address' => $address->address,
                'promotions' => json_encode([]),
                'coupon' => json_encode([]),
                'sub_amount' => $row['sub_total'],
                'discount' => 0,
                'amount' => $row['total'],
                'notes' => $row['customerNote']??null,
                'distance' => OrdersHelper::haversineDistance(
                    $restaurant->latitude,
                    $restaurant->longitude,
                    $row['lat'],
                    $row['long']
                )
            ];

            Order::create($orderData);
        }
    }
}
