<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Courier;
use App\Models\CourierOrder;
use App\Models\Admin;
use App\Models\Restaurant;
use App\Models\Location;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function verifyOrderCode(Request $request): JsonResponse
    {
        $vtoken = "3b44111837d8e28e846f4dc9dbac986cb0010e3e";

        $trackingId = $request->trackingId;
        $rToken = $request->token;
        $code = $request->code;
        $courierId = $request->courierId;

        if($rToken == $vtoken) {
            $order = Order::where('tracking_id',$trackingId)->first();

            if (!$order){
                return response()->json(['message' => 'Sipariş Bulunamadı'], 404);
            }

            if (Order::where('verify_code', $code)->where('id',$order->id)->exists()) {
                $courier = Courier::where('id', $courierId)->first();

                if ($courier){
                    $courier->situation = "Aktif";
                    $courier->save();
                }

                $auto = Admin::find(1);

                if($auto->auto_orders == 1){
                    if ($order){
                        $atama = new CourierOrder();
                        $atama->courier_id = $courierId;
                        $atama->order_id = $order->id;
                        $atama->save();
                    }
                }

                $order->status = 'DELIVERED';
                $order->verify_code = null;
                $order->save();

                return response()->json(['message' => 'Sipariş Başarıyla Doğrulandı'], 200);
            }else {
                return response()->json(['message' => 'Doğrulama Kodu Eşleşmiyor'], 404);
            }
        }

        return response()->json(['message' => 'Token Geçersiz',401]);
    }

    public function orders($token, $id): JsonResponse
    {
        $vtoken = "3b44111837d8e28e846f4dc9dbac986cb0010e3e";
        if ($token == $vtoken) {
            $kuryen = CourierOrder::where('courier_id', $id)->get();

            if (count($kuryen) > 0) {
                $kuryeOrders = [];

                foreach ($kuryen as $kurye) {
                    $orders = Order::where('id', $kurye->order_id)->whereDate('created_at', Carbon::today())->get();

                    foreach ($orders as $key => $order) {
                        $restaurant = Restaurant::where('id', $order->restaurant_id)->first();

                        $data = [
                            "tracking_id" => $order->tracking_id,
                            "full_name" => $order->full_name,
                            "restaurant_name" => $restaurant->restaurant_name,
                            "phone" => $order->phone,
                            "address" => $order->address,
                            "amount" => number_format($order->amount, 2) . " TL",
                            "platform" => $order->platform,
                            "time" => Carbon::createFromFormat('Y-m-d H:i:s', $order->created_at)->format('H:i'),
                            "payment_method" => $order->payment_method,
                            "status" => $order->status
                        ];

                        array_push($kuryeOrders, $data);
                    }
                }

                return response()->json(['orders' => $kuryeOrders]);
            } else {
                $getOrders = [];

                $orders = Order::where('courier_id', $id)->whereDate('created_at', Carbon::today())->get();

                foreach ($orders as $key => $order) {

                    $restaurant = Restaurant::where('id', $order->restaurant_id)->first();

                    $data = [
                        "tracking_id" => $order->tracking_id,
                        "full_name" => $order->full_name,
                        "restaurant_name" => $restaurant->restaurant_name,
                        "phone" => $order->phone,
                        "address" => $order->address,
                        "amount" => number_format($order->amount, 2) . " TL",
                        "platform" => $order->platform,
                        "time" => Carbon::createFromFormat('Y-m-d H:i:s', $order->created_at)->format('H:i'),
                        "payment_method" => $order->payment_method,
                        "status" => $order->status
                    ];

                    array_push($getOrders, $data);
                }

                return response()->json(['orders' => $getOrders]);
            }
        } else {
            return response()->json(['status' => 'ERROR', 'message' => 'Token uyuşmazlığı.']);
        }
    }

    public function login(Request $request): JsonResponse
    {
        $vtoken = "3b44111837d8e28e846f4dc9dbac986cb0010e3e";
        if ($request->token == $vtoken) {
            $courier = Courier::where('phone', $request->phone)->where('password', $request->password)->first();

            return response()->json(['courier' => $courier]);
        } else {
            return response()->json(['status' => 'ERROR', 'message' => 'Token uyuşmazlığı.']);
        }
    }

    public function location(Request $request): JsonResponse
    {
        $vtoken = "3b44111837d8e28e846f4dc9dbac986cb0010e3e";
        if ($request->token == $vtoken) {
            $local = Location::where('courier_id', $request->courier_id)->first();
            $kuryen = CourierOrder::where('courier_id', $request->courier_id)->whereDate('created_at', Carbon::today())->get();

            $total_orders = 0;

            foreach ($kuryen as $key => $value) {
                $order = Order::where('id', $value->order_id)->where('status', '!=', 'HANDOVER')->where('status', '!=', 'DELIVERED')->where('okundu', 0)->first();
                if ($order) {

                    $total_orders++;
                    $order->okundu = 1;
                    $order->save();

                }
            }

            //$total_orders = count($kuryen);

            if ($local) {
                $local->latitude = $request->latitude;
                $local->longitude = $request->longitude;
                $local->save();
            } else {

                $location = new Location();
                $location->courier_id = $request->courier_id;
                $location->latitude = $request->latitude;
                $location->longitude = $request->longitude;
                $location->save();
            }

            return response()->json(['status' => 'OK', 'new_orders' => $total_orders]);
        } else {
            return response()->json(['status' => 'ERROR', 'message' => 'Token uyuşmazlığı.']);
        }
    }

    public function order_status(Request $request): JsonResponse
    {
        $vtoken = "3b44111837d8e28e846f4dc9dbac986cb0010e3e";
        if ($request->token == $vtoken) {

            $order = Order::where('tracking_id', $request->tracking_id)->first();
            $order->status = $request->status;
            $order->save();

            $courier = Courier::where('id', $request->courier_id)->first();

            if ($request->status == "DELIVERED") {
                $courier->situation = "Aktif";
                $courier->save();
                $auto = Admin::find(1);

                if ($auto->auto_orders == 1) {
                    $orders = Order::where('status', '!=', 'DELIVERED')->where('courier_id', -1)->whereDate('created_at', Carbon::today())->first();

                    $atama = new CourierOrder();
                    $atama->courier_id = $request->courier_id;
                    $atama->order_id = $orders->id;
                    $atama->save();
                }
            }
            if ($request->status == "HANDOVER") {
                $courier->situation = "Serviste";
                $courier->save();
            }

            return response()->json(['status' => 'OK', 'message' => 'Sipariş durumu düncellendi.']);

        } else {
            return response()->json(['status' => 'ERROR', 'message' => 'Token uyuşmazlığı.']);
        }
    }
    public function situation($token, $id): JsonResponse
    {
        $vtoken = "3b44111837d8e28e846f4dc9dbac986cb0010e3e";
        if ($token == $vtoken) {
            $kuryen = CourierOrder::where('courier_id', $id)->get();

            $t_orders = 0;
            $total = 0;
            $online = 0;
            $cash = 0;
            $credit_cart = 0;

            foreach ($kuryen as $kurye) {

                $t_orders1 = count(Order::where('id', $kurye->order_id)->whereDate('created_at', Carbon::today())->get());
                $total1 = Order::where('id', $kurye->order_id)->whereDate('created_at', Carbon::today())->sum('amount');
                $online1 = Order::where('id', $kurye->order_id)->whereDate('created_at', Carbon::today())->where('payment_method', 'Online Kredi/Banka Kartı')->sum('amount');
                $cash1 = Order::where('id', $kurye->order_id)->whereDate('created_at', Carbon::today())->where('payment_method', 'Kapıda Nakit ile Ödeme')->sum('amount');
                $credit_cart1 = Order::where('id', $kurye->order_id)->whereDate('created_at', Carbon::today())->where('payment_method', 'Kapıda Kredi Kartı ile Ödeme')->sum('amount');

                $t_orders = $t_orders + $t_orders1;
                $total = $total + $total1;
                $online = $online + $online1;
                $cash = $cash + $cash1;
                $credit_cart = $credit_cart + $credit_cart1;
            }

            $data = [
                "t_orders" => $t_orders,
                "total" => number_format($total, 2) . " TL",
                "cash" => number_format($cash, 2) . " TL",
                "online" => number_format($online, 2) . " TL",
                "credit_cart" => number_format($credit_cart, 2) . " TL",
            ];

            return response()->json(['situation' => $data]);
        } else {
            return response()->json(['status' => 'ERROR', 'message' => 'Token uyuşmazlığı.']);
        }
    }

    public function status($token, $id, $status): JsonResponse
    {
        $vtoken = "3b44111837d8e28e846f4dc9dbac986cb0010e3e";
        if ($token == $vtoken) {

            $courier = Courier::where('id', $id)->first();
            $courier->situation = $status;
            $courier->save();

            return response()->json(['status' => "OK"]);
        } else {
            return response()->json(['status' => 'ERROR', 'message' => 'Token uyuşmazlığı.']);
        }
    }

    public function getLocations(): JsonResponse
    {
        $locations = Location::where('courier_id', '>', 0)->get();
        $getData = [];

        foreach ($locations as $location) {

            $courier = Courier::where('id', $location->courier_id)->where('restaurant_id', 0)->where('situation', '!=', null)->first();

            if ($courier) {
                $data = [
                    "id" => $courier->id,
                    "name" => $courier->name,
                    "lat" => (float)$location->latitude,
                    "lng" => (float)$location->longitude,
                    "description" => $courier->situation
                ];

                array_push($getData, $data);
            }
        }

        return response()->json($getData);
    }

    public function getCourierOrder()
    {
        $admin = Admin::find(1);
        if ($admin->auto_orders == 7) {
            $courier = Courier::where('situation', 'Aktif')->first();

            if ($courier) {
                $order = Order::where('courier_id', -1)->where('status', 'PREPARED')->where('okundu', 0)->latest()->first();
                if ($order) {
                    $ordercourier = CourierOrder::where('order_id', $order->id)->first();
                    if (!$ordercourier) {

                        $new = new CourierOrder();
                        $new->courier_id = $courier->id;
                        $new->order_id = $order->id;
                        $new->save();

                        $courier->situation = "Serviste";
                        $courier->save();
                    }
                }
            }
        }
    }

    public function reports(Request $request): JsonResponse
    {
        $vtoken = "3b44111837d8e28e846f4dc9dbac986cb0010e3e";

        // Token doğrulama
        if ($request->input('token') !== $vtoken) {
            return response()->json(['status' => 'ERROR', 'message' => 'Token uyuşmazlığı.'], 403);
        }

        // Parametrelerin kontrolü
        $courierId = $request->input('courier_id');
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');

        if (!$courierId || !$startDate || !$endDate) {
            return response()->json(['status' => 'ERROR', 'message' => 'Eksik parametreler.'], 400);
        }

        // Tarihlerin formatlanması
        $startDate = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
        $endDate = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();

        // Kurye ve sipariş verilerinin alınması
        $courier = Courier::find($courierId);
        if (!$courier) {
            return response()->json(['status' => 'ERROR', 'message' => 'Kurye bulunamadı.'], 404);
        }

        $orderCount = CourierOrder::where('courier_id', $courier->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $totalProgressPayment = $courier->price * $orderCount;

        // JSON cevabı
        return response()->json([
            'courier_name' => $courier->name,
            'order_count' => $orderCount,
            'total_progress_payment' => $totalProgressPayment
        ]);
    }
}
