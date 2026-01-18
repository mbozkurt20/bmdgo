<?php
namespace App\Http\Controllers\Api;

use App\Helpers\OrdersHelper;
use App\Helpers\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Restaurant;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Jobs\AssignOrderToCourier;

class OrderController extends Controller
{
    public function addOrder(Request $request)
    {
        Log::info('Gelen Data', (array)json_encode($request->all()));

        $order = Order::where('tracking_id', $row['order_code'])->first();
        if ($order) {
            continue;
        }

        $address = json_decode($row['address']);
        $create  = Customer::where('email',$row['customer']['email'])->first();
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

    public function cancelOrder(Request $request)
    {
        Log::info('İptal Edilen Data', (array)json_encode($request->all()));
    }
}
