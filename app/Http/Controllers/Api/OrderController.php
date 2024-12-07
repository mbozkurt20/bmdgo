<?php
namespace App\Http\Controllers\Api;

use App\Helpers\OrdersHelper;
use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Jobs\AssignOrderToCourier;

class OrderController extends Controller
{
    public function addOnlineOrder(Request $request){

        $order = $request->input('order');

        Log::info("response: ". json_encode($order));

        $restaurant = Restaurant::where('entegra_id', $order['restaurant_id'])->first();
        $address = json_decode($order['client']['delivery_address']);
        $address = $address->address;
        $paymentMethod = $this->getPaymentMethod($order['provider']['slug'], $order['payment_method']);
        if ($restaurant){
            $items = [];
            if(isset($order['items'])){
                foreach ($order['items'] as $product) {
                    $addItem = [];
                    $addItem['price'] = $product['price'];
                    $addItem['count'] = $product['count'];
                    $addItem['unitSellingPrice'] = $product['total_price_with_option'];
                    $addItem['name'] = $product['name'];
                    $addItem['items'][0]['packageItemId'] = "ahtaPOS";
                    $items[] = $addItem;
                }
            }

            $orderData = [
                'platform'       => $this->enrichmentPlatform($order['provider']['slug']),
                'courier_id'     => 0,
                'status'         => 'PENDING',
                'restaurant_id'  => $restaurant->id,
                'tracking_id'    => $order['pid'],
                'full_name'      => $order['client']['name'],
                'phone'          =>  $order['client']['client_phone_number'],
                'amount'         => $order['total_discounted_price'],
                'payment_method' => $paymentMethod,
                'items'          => json_encode($items),
                'address'        => $address,
                'notes'          => $order['client_note']
            ];
            $createOrder = Order::create($orderData);
            if ($createOrder){
                AssignOrderToCourier::dispatch($createOrder);

                //sipariş eklenince ses bildirimi gerçekleştirir.
                OrdersHelper::createOrderNotification($createOrder);

                return response()->json(['message' => 'Sipariş oluşturuldu'], 200);
            }
        }

        return response()->json(['message' => 'Sipariş oluşturulamadı!'], 422);


    }

    public function enrichmentPlatform($slug){
        $platforms = ["ys" => "yemeksepeti ", "ty" => "trendyol"];
        return isset($platforms[$slug]) ?  $platforms[$slug] : $slug;
    }


    public function  getPaymentMethod($slug, $id){
        $paymentMethods=["ys"=>[["id"=>"1","name"=>"Kapıda Nakit ile Ödeme"],["id"=>"2","name"=>"Kapıda Ticket ile Ödeme"],["id"=>"3","name"=>"Kapıda Ticket ile Ödeme"],["id"=>"4","name"=>"Online Kredi/Banka Kartı"],["id"=>"5","name"=>"Kapıda Ticket ile Ödeme"],["id"=>"6","name"=>"Kapıda Kredi Kartı ile Ödeme"],["id"=>"10","name"=>"Kapıda Ticket ile Ödeme"],["id"=>"11","name"=>"Kapıda Ticket ile Ödeme"],["id"=>"15","name"=>"Kapıda Ticket ile Ödeme"],["id"=>"18","name"=>"Kapıda Ticket ile Ödeme"],["id"=>"23","name"=>"Online Kredi/Banka Kartı"],["id"=>"24","name"=>"Kapıda Ticket ile Ödeme"],["id"=>"25","name"=>"Kapıda Ticket ile Ödeme"],["id"=>"26","name"=>"Kapıda Ticket ile Ödeme"],["id"=>"27","name"=>"Kapıda Ticket ile Ödeme"],["id"=>"28","name"=>"Kapıda Ticket ile Ödeme"],["id"=>"29","name"=>"Online Kredi/Banka Kartı"]],"ty"=>[["id"=>"1","name"=>"Online Kredi/Banka Kartı"],["id"=>"2","name"=>"Online Kredi/Banka Kartı"],["id"=>"3","name"=>"Online Kredi/Banka Kartı"],["id"=>"4","name"=>"Online Kredi/Banka Kartı"]],"getir"=>[["id"=>"1","name"=>"Online Kredi/Banka Kartı"],["id"=>"2","name"=>"Kapıda Ticket ile Ödeme"],["id"=>"3","name"=>"Kapıda Kredi Kartı ile Ödeme"],["id"=>"4","name"=>"Kapıda Nakit ile Ödeme"],["id"=>"5","name"=>"Kapıda Ticket ile Ödeme"],["id"=>"6","name"=>"Kapıda Ticket ile Ödeme"],["id"=>"7","name"=>"Kapıda Ticket ile Ödeme"],["id"=>"8","name"=>"Kapıda Ticket ile Ödeme"],["id"=>"9","name"=>"Kapıda Ticket ile Ödeme"],["id"=>"10","name"=>"Kapıda Ticket ile Ödeme"],["id"=>"11","name"=>"Kapıda Ticket ile Ödeme"],["id"=>"12","name"=>"Kapıda Ticket ile Ödeme"],["id"=>"15","name"=>"Kapıda Ticket ile Ödeme"],["id"=>"16","name"=>"Online Kredi/Banka Kartı"],["id"=>"17","name"=>"Kapıda Ticket ile Ödeme"],["id"=>"18","name"=>"Kapıda Ticket ile Ödeme"],["id"=>"19","name"=>"Kapıda Ticket ile Ödeme"],["id"=>"21","name"=>"Kapıda Ticket ile Ödeme"],["id"=>"22","name"=>"Online Kredi/Banka Kartı"],["id"=>"24","name"=>"Online Kredi/Banka Kartı"]],"migros"=>[["id"=>1,"name"=>"Kapıda Ticket ile Ödeme"],["id"=>2,"name"=>"Online Kredi/Banka Kartı"],["id"=>3,"name"=>"Online Kredi/Banka Kartı"],["id"=>4,"name"=>"Kapıda Ticket ile Ödeme"],["id"=>5,"name"=>"Kapıda Kredi Kartı ile Ödeme"],["id"=>6,"name"=>"Kapıda Nakit ile Ödeme"],["id"=>7,"name"=>"Kapıda Ticket ile Ödeme"],["id"=>8,"name"=>"Kapıda Ticket ile Ödeme"],["id"=>9,"name"=>"Kapıda Ticket ile Ödeme"],["id"=>10,"name"=>"Kapıda Ticket ile Ödeme"],["id"=>11,"name"=>"Kapıda Ticket ile Ödeme"],["id"=>12,"name"=>"Kapıda Ticket ile Ödeme"]]];
        if ($paymentMethods[$slug]){
            $column = array_column($paymentMethods[$slug], "id");
            $index = array_search($id, $column);

            if ($index !== false) {
                $result = $paymentMethods[$slug][$index]['name'];
                return $result;
            }
        }
        return 'bilinmiyor';
    }
}
