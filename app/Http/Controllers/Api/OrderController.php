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

class OrderController extends Controller
{
    public function addOrder(Request $request)
    {
        Log::info('Gelen Data', (array)json_encode($request->all()));

        $orderData = $request->json()->all();

        $address = $orderData['client']['deliveryAddress'];
        $restaurant = Restaurant::where('entegra_restaurant_id',$orderData['restaurantId'])->first();

        $create  = Customer::where('email',$orderData['client']['id'])->first();
        if (!$create) {
            $create = new Customer();
            $create->restaurant_id = $restaurant->id; // Assuming the authenticated user is the restaurant
            $create->name =  $orderData['client']['name'];
            $create->phone = $orderData['client']['clientPhoneNumber'];
            $create->mobile = $orderData['client']['contactPhoneNumber'];
            $create->email = $orderData['client']['id'] ?? null;
            $create->save();

            if ($create) {
                $addr = new CustomerAddress();
                $addr->customer_id = $create->id;
                $addr->restaurant_id = $restaurant->id;
                $addr->name = $orderData['client']['name'];
                $addr->sokak_cadde =  $orderData['client']['deliveryAddress']['street'] ?? " ";
                $addr->bina_no = $orderData['client']['deliveryAddress']['aptNo'] ?? " ";
                $addr->city_id = ' ';
                $addr->kat = $orderData['client']['deliveryAddress']['floor'];;
                $addr->latitude = $orderData['client']['location']['lat'];
                $addr->longitude =$orderData['client']['location']['lon'];
                $addr->daire_no = $orderData['client']['deliveryAddress']['doorNo'];
                $addr->mahalle = ' ';
                $addr->adres_tarifi = $orderData['client']['deliveryAddress']['address'] ?? '';
                $addr->save();
            }
        }

        $items = [];

        foreach ($orderData['products'] as $item) {
            $items[] = [
                'price' => $item['totalPrice'],              // toplam fiyat
                'unitSellingPrice' => $item['price'],   // birim fiyat
                'totalOptionPrice' => $item['totalOptionPrice'],   // birim fiyat
                'totalPriceWithOption' => $item['totalPriceWithOption'],   // birim fiyat
                'quantity' => (string) $item['count'],    // string isteniyorsa
                'name' => $item['name']['tr'],        // ürün adı
                'image' => " ",        // ürün adı
            ];
        }

        $orderData = [
            'platform' => $orderData['provider']['slug'],
            'customer_id' => $create->id,
            'pid' => $orderData['pid'] ?? null,
            'status_request_count' => 0,
            'restaurant_id' => $restaurant->id,
            'courier_id' => -1,
            'status' => OrderStatus::PREPARED,
            'tracking_id' => $orderData['shortCode'],
            'full_name' => $orderData['client']['name'],
            'phone' =>  $orderData['client']['contactPhoneNumber'],
            'payment_method' => $orderData['paymentMethodText']['tr'],
            'items' => json_encode($items),
            'address' => $orderData['client']['deliveryAddress']['address'],
            'promotions' => json_encode([]),
            'coupon' => json_encode([]),
            'sub_amount' => $orderData['totalPrice'],
            'discount' => $orderData['totalDiscount'],
            'amount' => $orderData['totalDiscountedPrice'],
            'notes' => $orderData['clientNote']??null,
            'platform_date' => date('d-m-Y, H:i', strtotime($orderData['created_at'])),
            'distance' => OrdersHelper::haversineDistance(
                $restaurant->latitude,
                $restaurant->longitude,
                $orderData['client']['location']['lat'],
                $orderData['client']['location']['lon']
            )
        ];

        $order = Order::create($orderData);

        return response()->json(['success' => true, 'order' => $order]);
    }

    public function cancelOrder(Request $request)
    {
        $orderData = $request->json()->all();

        $order = Order::where('tracking_id' , $orderData['shortCode'])->first();

        $order->update([
            'status' => OrderStatus::UNSUPPLIED
        ]);

        return response()->json(['success' => true, 'order' => $order]);
    }
}
