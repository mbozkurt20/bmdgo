<?php

namespace App\Http\Controllers;

use App\Helpers\OrderStatus;
use App\Helpers\CourierStatus;
use App\Helpers\OrdersHelper;
use App\Models\Courier;
use App\Models\CourierOrder;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\Restaurant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GpsYemekInboundController extends Controller
{
    public function handle(Request $request)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $restaurant = Restaurant::where('gpsyemek_api_key', $token)->first();

        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Restaurant not found'], 404);
        }

        $event = $request->input('event');

        switch ($event) {
            case 'new_order':
                return $this->handleNewOrder($request, $restaurant);

            case 'order_status_changed':
                return $this->handleOrderStatusChanged($request, $restaurant);

            default:
                return response()->json(['success' => false, 'message' => 'Unknown event'], 400);
        }
    }

    private function handleNewOrder(Request $request, Restaurant $restaurant)
    {
        $row = $request->input('order');

        if (!$row) {
            return response()->json(['success' => false, 'message' => 'No order data'], 400);
        }

        $existing = Order::where('tracking_id', $row['order_code'])->first();
        if ($existing) {
            return response()->json(['success' => true, 'message' => 'Already exists']);
        }

        if ($restaurant->admin->top_up_balance <= 0) {
            OrdersHelper::updateTopup($restaurant->admin->id, $restaurant->id);
            return response()->json(['success' => false, 'message' => 'Insufficient balance'], 402);
        }

        $address = json_decode($row['address'] ?? '{}');

        $customer = Customer::where('email', $row['customer']['email'] ?? '')->first();

        if (!$customer) {
            $customer                = new Customer();
            $customer->restaurant_id = $restaurant->id;
            $customer->name          = ($row['customer']['first_name'] ?? '') . ' ' . ($row['customer']['last_name'] ?? '');
            $customer->phone         = $row['customer']['phone'] ?? null;
            $customer->email         = $row['customer']['email'] ?? null;
            $customer->save();

            $addr                = new CustomerAddress();
            $addr->customer_id   = $customer->id;
            $addr->restaurant_id = $restaurant->id;
            $addr->name          = $customer->name . '-GpsYemek';
            $addr->sokak_cadde   = ' ';
            $addr->bina_no       = $address->apartment ?? '';
            $addr->city_id       = ' ';
            $addr->kat           = ' ';
            $addr->latitude      = $row['lat'] ?? null;
            $addr->longitude     = $row['long'] ?? null;
            $addr->daire_no      = ' ';
            $addr->mahalle       = ' ';
            $addr->adres_tarifi  = $address->address ?? '';
            $addr->save();
        }

        $items = [];
        foreach ($row['items'] ?? [] as $item) {
            $items[] = [
                'price'            => $item['item_total'],
                'unitSellingPrice' => $item['unit_price'],
                'discountedPrice'  => $item['discounted_price'],
                'quantity'         => (string) $item['quantity'],
                'name'             => $item['menu_item']['name'],
                'image'            => $item['menu_item']['image'],
                'features'        => json_encode($item['menu_item'] ?? []),
                'product_options' => json_encode($item['options'] ?? []),
                'restaurant_id'    => $item['restaurant_id'],
            ];
        }

        Order::create([
            'platform'       => 'gpsyemek',
            'customer_id'    => $customer->id,
            'restaurant_id'  => $restaurant->id,
            'courier_id'     => -1,
            'status'         => OrderStatus::PENDING,
            'tracking_id'    => $row['order_code'],
            'full_name'      => $customer->name,
            'phone'          => ($row['customer']['first_name'] ?? '') . '/' . substr($row['order_code'], -11, 11),
            'payment_method' => $row['payment_method_name'] ?? null,
            'items'          => json_encode($items),
            'address'        => $address->address ?? '',
            'promotions'     => json_encode([]),
            'coupon'         => json_encode([]),
            'sub_amount'     => $row['sub_total'] ?? 0,
            'discount'       => 0,
            'amount'         => $row['total'] ?? 0,
            'notes'          => $row['customerNote'] ?? null,
            'platform_date'  => date('d-m-Y, H:i', strtotime($row['created_at'] ?? 'now')),
            'distance'       => OrdersHelper::haversineDistance(
                $restaurant->latitude,
                $restaurant->longitude,
                $row['lat'] ?? 0,
                $row['long'] ?? 0
            ),
            'created_at'      => Carbon::now(),
        ]);

        return response()->json(['success' => true]);
    }

    private function handleOrderStatusChanged(Request $request, Restaurant $restaurant)
    {
        $orderCode = $request->input('order_code');
        $status    = $request->input('status');

        $order = Order::where('tracking_id', $orderCode)
            ->where('restaurant_id', $restaurant->id)
            ->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        if (in_array($status, [OrderStatus::DELIVERED, OrderStatus::UNSUPPLIED, 'CANCELLED'])) {
            $courierOrder = CourierOrder::where('order_id', $order->id)->first();
            if ($courierOrder) {
                $courier = Courier::find($courierOrder->courier_id);
                if ($courier) {
                    $courier->status = CourierStatus::active;
                    $courier->save();
                    Log::info('Kurye durumu güncellendi', ['courier_id' => $courier->id]);
                }
            }

            if ($status === OrderStatus::UNSUPPLIED) {
                $order->courier_id  = -1;
                $order->assigned_at = null;
            }
        }

        // bmd-kurye'de CANCELLED sabiti yok, UNSUPPLIED olarak işle
        if ($status === 'CANCELLED') {
            $status = OrderStatus::UNSUPPLIED;
        }

        $order->status = $status;
        $order->save();

        return response()->json(['success' => true]);
    }
}
