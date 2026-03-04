<?php

namespace App\Http\Controllers;

use App\Helpers\CourierStatus;
use App\Helpers\NotificationHelper;
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
    const GPS_YEMEK_URL = 'https://gpsyemek.com/api/v1/webhook/orders';

    function index()
    {
        $restaurants = Restaurant::all();
        foreach ($restaurants as $restaurant) {
            $this->orders($restaurant);
        }
    }

    public function updateOrder(Request $request)
    {
        $status    = $request->input('action');
        $orderCode = $request->input('tracking_id');
        $order     = Order::where('tracking_id', $orderCode)->first();

        if (in_array($status, [OrderStatus::PREPARED, OrderStatus::UNSUPPLIED, OrderStatus::DELIVERED, OrderStatus::HANDOVER])) {
            $api_token = $order->restaurant->gpsyemek_api_key;

            $response = Http::withToken($api_token)
                ->timeout(5)
                ->post(self::GPS_YEMEK_URL, [
                    'event'      => 'order_updated',
                    'order_code' => $orderCode,
                    'status'     => $status,
                ]);

            $result = $response->json();

            if ($result['success'] ?? false) {
                switch ($status) {
                    case 'DELIVERED':
                        $courierOrder = CourierOrder::where('order_id', $order->id)->first();
                        if ($courierOrder) {
                            $courier = Courier::find($courierOrder->courier_id);
                            if ($courier) {
                                $courier->status = CourierStatus::active;
                                $courier->update();
                                Log::info('Kurye durumu güncellendi', ['courier_id' => $courier->id]);
                            }
                        }
                        break;

                    case 'UNSUPPLIED':
                        $courierOrder = CourierOrder::where('order_id', $order->id)->first();
                        if ($courierOrder) {
                            $courier = Courier::find($courierOrder->courier_id);
                            if ($courier) {
                                $courier->status = CourierStatus::active;
                                $courier->save();

                                $order->courier_id  = -1;
                                $order->assigned_at = null;
                                $order->status      = OrderStatus::PREPARED;
                                $order->update();

                                Log::info('Kurye durumu güncellendi', ['courier_id' => $courier->id]);

                                if (OrdersHelper::getOrderSystem(3)) {
                                    NotificationHelper::add([
                                        'title'       => 'Kurye Paketi Reddetti',
                                        'description' => $order->tracking_id . ' takip numaralı paket ' . $courier->name . ' kurye tarafından teslim edildi.',
                                        'url'         => route('admin.balance'),
                                    ]);
                                }
                            }
                        }
                        break;
                }

                $order->status = $status;
                if ($order->update()) {
                    return response()->json(['status' => 'OK'], 200);
                }

                Log::error('Sipariş durumu güncellenemedi', ['order_id' => $order->id]);
                return response()->json(['status' => 'ERR'], 400);
            }

            return response()->json(['status' => ''], 200);
        }

        $order->status = $status;
        if ($order->update()) {
            return response()->json(['status' => 'OK'], 200);
        }

        Log::error('Sipariş durumu güncellenemedi', ['order_id' => $order->id]);
        return response()->json(['status' => 'ERR'], 400);
    }

    /**
     * GPS Yemek'te restoranı aç veya süresiz kapat.
     * AJAX ile çağrılır.
     */
    public function toggleStatus(Request $request)
    {
        $restaurant = Restaurant::find(Auth::user()->id);

        if (!$restaurant || blank($restaurant->gpsyemek_api_key)) {
            return response()->json(['success' => false, 'message' => 'GPS Yemek API key bulunamadı'], 400);
        }

        $close = (bool) $request->input('close'); // true = kapat, false = aç
        $event = $close ? 'restaurant_permanently_close' : 'restaurant_open';

        $response = Http::withToken($restaurant->gpsyemek_api_key)
            ->timeout(5)
            ->post(self::GPS_YEMEK_URL, ['event' => $event]);

        $result = $response->json();

        if ($result['success'] ?? false) {
            $restaurant->gpsyemek_is_open = !$close;
            $restaurant->save();

            return response()->json(['success' => true, 'is_open' => !$close]);
        }

        return response()->json(['success' => false, 'message' => 'GPS Yemek yanıt vermedi'], 500);
    }

    private function orders($restaurant)
    {
        if ($restaurant->admin->top_up_balance <= 0) {
            OrdersHelper::updateTopup($restaurant->admin->id, $restaurant->id);
            return;
        }

        $api_token = $restaurant->gpsyemek_api_key;

        if (blank($api_token)) {
            return;
        }

        $response = Http::withToken($api_token)
            ->timeout(5)
            ->post(self::GPS_YEMEK_URL, ['event' => 'get_orders']);

        $data = $response->json();

        // Restoran açık/kapalı durumunu senkronize et
        if (isset($data['restaurant_status'])) {
            $isOpen = !($data['restaurant_status']['permanently_closed'] ?? false);
            if ($restaurant->gpsyemek_is_open !== $isOpen) {
                $restaurant->gpsyemek_is_open = $isOpen;
                $restaurant->save();
            }
        }

        $orders = $data['orders'] ?? [];

        foreach ($orders as $row) {
            $order = Order::where('tracking_id', $row['order_code'])->first();
            if ($order) {
                continue;
            }

            $address = json_decode($row['address']);
            $create  = Customer::where('email', $row['customer']['email'])->first();

            if (!$create) {
                $create                = new Customer();
                $create->restaurant_id = $restaurant->id;
                $create->name          = $row['customer']['first_name'] . ' ' . $row['customer']['last_name'];
                $create->phone         = $row['customer']['phone'];
                $create->mobile        = $row['mobile'] ?? null;
                $create->email         = $row['customer']['email'] ?? null;
                $create->save();

                if ($create) {
                    $addr                = new CustomerAddress();
                    $addr->customer_id   = $create->id;
                    $addr->restaurant_id = $restaurant->id;
                    $addr->name          = $row['customer']['first_name'] . ' ' . $row['customer']['last_name'] . '-GpsYemek';
                    $addr->sokak_cadde   = ' ';
                    $addr->bina_no       = $address->apartment ?? '';
                    $addr->city_id       = ' ';
                    $addr->kat           = ' ';
                    $addr->latitude      = $row['lat'];
                    $addr->longitude     = $row['long'];
                    $addr->daire_no      = ' ';
                    $addr->mahalle       = ' ';
                    $addr->adres_tarifi  = $address->address ?? '';
                    $addr->save();
                }
            }

            $items = [];
            foreach ($row['items'] as $item) {
                $items[] = [
                    'price'            => $item['item_total'],
                    'unitSellingPrice' => $item['unit_price'],
                    'discountedPrice'  => $item['discounted_price'],
                    'quantity'         => (string) $item['quantity'],
                    'name'             => $item['menu_item']['name'],
                    'image'            => $item['menu_item']['image'],
                    'restaurant_id'    => $item['restaurant_id'],
                ];
            }

            Order::create([
                'platform'       => 'gpsyemek',
                'customer_id'    => $create->id,
                'restaurant_id'  => $restaurant->id,
                'courier_id'     => -1,
                'status'         => OrderStatus::PENDING,
                'tracking_id'    => $row['order_code'],
                'full_name'      => $row['customer']['first_name'] . ' ' . $row['customer']['last_name'],
                'phone'          => $row['customer']['first_name'] . '/' . substr($row['order_code'], -11, 11),
                'payment_method' => $row['payment_method_name'],
                'items'          => json_encode($items),
                'address'        => json_decode($row['address'])->address ?? '',
                'promotions'     => json_encode([]),
                'coupon'         => json_encode([]),
                'sub_amount'     => $row['sub_total'],
                'discount'       => 0,
                'amount'         => $row['total'],
                'notes'          => $row['customerNote'] ?? null,
                'platform_date'  => date('d-m-Y, H:i', strtotime($row['created_at'])),
                'distance'       => OrdersHelper::haversineDistance(
                    $restaurant->latitude,
                    $restaurant->longitude,
                    $row['lat'],
                    $row['long']
                ),
            ]);
        }
    }
}
