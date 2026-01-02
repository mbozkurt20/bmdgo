<?php

namespace App\Http\Controllers;

use App\Helpers\CourierStatus;
use App\Helpers\OrdersHelper;
use App\Helpers\OrderStatus;
use App\Models\City;
use App\Models\Courier;
use App\Models\CourierOrder;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GpsYemekController extends Controller
{
    function index()
    {
        $restaurants = Restaurant::all();
        foreach ($restaurants as $restaurant) {
            $this->orders($restaurant);
        }
    }

    public function updateOrder(Request $request)
    {
        $orderCode = $request->input('tracking_id');
        $status = $request->input('action');

        $order = Order::where('tracking_id', $orderCode)->first();
        $restaurant = Restaurant::where('id', $order->restaurant_id)->first();
        $orderId = $order->id;
        $api_token = $order->restaurant->gpsyemek_api_key;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $api_token
        ])->post('https://gpsyemek.com/api/v1/webhook/orders', [
            'event' => 'order_updated',
            'order_code' => $orderCode,
            'status' => $status,
        ]);

        $response = json_decode($response->body());

        if ($response->success) {
            // API'ye gönderilecek veriyi belirle
            switch ($status) {
                case 'DELIVERED':
                    // Sipariş teslim edildiğinde kuryenin durumu güncelleniyor
                    $courierOrder = CourierOrder::where('order_id', $order->id)->first();
                    if ($courierOrder) {
                        $courier = Courier::find($courierOrder->courier_id);
                        if ($courier) {
                            // Kuryenin durumunu güncelle
                            $courier->status = CourierStatus::active;;
                            $courier->update();
                            Log::info('Kurye durumu güncellendi', ['courier_id' => $courier->id]);
                        }
                    }
                    break;
                case 'UNSUPPLIED':
                    // Sipariş iptal edildiyse kuryenin durumu güncelleniyor
                    $courierOrder = CourierOrder::where('order_id', $order->id)->first();
                    if ($courierOrder) {
                        $courier = Courier::find($courierOrder->courier_id);
                        if ($courier) {
                            // Kuryenin durumunu güncelle
                            $courier->status = CourierStatus::active;;
                            $courier->save();
                            Log::info('Kurye durumu güncellendi', ['courier_id' => $courier->id]);
                        }
                    }
                    break;
            }

            $order->status = $status;
            $success = $order->update();

            if ($success) {
                return response()->json(['status' => "OK"], 200);
            } else {
                Log::error('Sipariş durumu güncellenemedi', ['order_id' => $order->id]);
                return response()->json(['status' => "ERR"], 400);
            }
        }else {
            return response()->json(['status' => ""], 200);
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

            $items = [];

            foreach ($row['items'] as $item) {
                $items[] = [
                    'price' => $item['item_total'],              // toplam fiyat
                    'unitSellingPrice' => $item['unit_price'],   // birim fiyat
                    'discountedPrice' => $item['discounted_price'],   // birim fiyat
                    'quantity' => (string) $item['quantity'],    // string isteniyorsa
                    'name' => $item['menu_item']['name'],        // ürün adı
                    'image' => $item['menu_item']['image'],        // ürün adı
                    'restaurant_id' => $item['restaurant_id'],        // ürün adı
                ];
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
                'payment_method' => $row['payment_method_name'],
                'items' => json_encode($items),
                'address' => json_decode($row['address'])->address ?? '',
                'promotions' => json_encode([]),
                'coupon' => json_encode([]),
                'sub_amount' => $row['sub_total'],
                'discount' => 0,
                'amount' => $row['total'],
                'notes' => $row['customerNote']??null,
                'platform_date' => date('d-m-Y, H:i', strtotime($row['created_at'])),

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
