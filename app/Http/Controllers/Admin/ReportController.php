<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Courier;
use App\Models\Restaurant;
use App\Models\Order;
use App\Models\CourierOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $couriers = Courier::where('status','active')->where('restaurant_id', 0)->where('admin_id', auth()->id())->get();
        $restaurants = Restaurant::where('status','active')->where('admin_id', auth()->id())->get();

        return view('admin.reports.index', compact('couriers','restaurants'));
    }


    public function globalFilter(Request $request){


        if($request->courier > 0){

            if($request->restaurant > 0){

                $couriers  = CourierOrder::where('courier_id', $request->courier)->get();

                $getData = [];

                $online = 0;
                $kapida_nakit = 0;
                $kapida_ticket = 0;
                $kapida_k_karti = 0;
                $topsiparis = 0;
 
                foreach ($couriers as $courier) {
                    $courierx = Courier::where('id', $courier->courier_id)->first();

                    $orders = Order::where('id', $courier->order_id)
                    ->where('restaurant_id', $request->restaurant)
                    ->whereDate('created_at', '>=', $request->start." 00:00:00")->whereDate('created_at', '<=', $request->end." 00:00:00")
                    ->first();

                    if($orders){

                        if($orders->payment_method == "Online Kredi/Banka Kartı"){
                                $online += $orders->amount;
                        }
                        if($orders->payment_method == "Kapıda Nakit ile Ödeme"){
                            $kapida_nakit += $orders->amount;
                        }
                        if($orders->payment_method == "Kapıda Ticket ile Ödeme"){
                            $kapida_ticket += $orders->amount;
                        }
                         if($orders->payment_method == "Kapıda Kredi Kartı ile Ödeme"){
                            $kapida_k_karti += $orders->amount;
                        }
                         
                        $topsiparis ++;

                        $data = [
                            "platform" => $orders->platform,
                            "courier" => $courierx->name,
                            "tracking_id" => $orders->tracking_id,
                            "message" => $orders->message,
                            "message2" => $orders->message2,
                            "full_name" => $orders->full_name,
                            "phone" => $orders->phone,
                            "payment" => $orders->payment_method,
                            "topsiparis" => $topsiparis,
                            "amount" => $orders->amount." TL", 
                            "online" => number_format($online, 2)." TL", 
                            "kapida_nakit" => number_format($kapida_nakit, 2)." TL", 
                            "kapida_ticket" => number_format($kapida_ticket, 2)." TL", 
                            "kapida_k_karti" => number_format($kapida_k_karti, 2)." TL", 
                            "time" => Carbon::parse($orders->created_at)->translatedFormat('d-m-Y- H:i')
                        ];

                        array_push($getData, $data);

                    }
                   

                }

                return response()->json(['data' => $getData]);

            }else{

                $couriers  = CourierOrder::where('courier_id', $request->courier)->get();

                $getData = [];
               

                $online = 0;
                $kapida_nakit = 0;
                $kapida_ticket = 0;
                $kapida_k_karti = 0;
                $topsiparis = 0;

                
                foreach ($couriers as $courier) {

                    $orders = Order::where('id', $courier->order_id)->whereDate('created_at', '>=', $request->start." 00:00:00")->whereDate('created_at', '<=', $request->end." 00:00:00")->first();
                    $courierx = Courier::where('id', $courier->courier_id)->first();
                    if($orders){

                         if($orders->payment_method == "Online Kredi/Banka Kartı"){
                                $online += $orders->amount;
                        }
                        if($orders->payment_method == "Kapıda Nakit ile Ödeme"){
                            $kapida_nakit += $orders->amount;
                        }
                        if($orders->payment_method == "Kapıda Ticket ile Ödeme"){
                            $kapida_ticket += $orders->amount;
                        }
                         if($orders->payment_method == "Kapıda Kredi Kartı ile Ödeme"){
                            $kapida_k_karti += $orders->amount;
                        }
                         
                        $topsiparis ++;
                    
                        $data = [
                            "platform" => $orders->platform,
                            "courier" => $courierx->name,
                            "tracking_id" => $orders->tracking_id,
                            "full_name" => $orders->full_name,
                            "phone" => $orders->phone,
                            "payment" => $orders->payment_method,
                            "amount" => $orders->amount." TL",
                            "topsiparis" => $topsiparis, 
                            "online" => number_format($online, 2)." TL", 
                            "kapida_nakit" => number_format($kapida_nakit, 2)." TL", 
                            "kapida_ticket" => number_format($kapida_ticket, 2)." TL", 
                            "kapida_k_karti" => number_format($kapida_k_karti, 2)." TL", 
                            "time" => Carbon::parse($orders->created_at)->translatedFormat('d-m-Y- H:i'),
                            "message" => $orders->message,
                            "message2" => $orders->message2
                        ];

                        array_push($getData, $data);

                    }
                   

                }

                return response()->json(['data' => $getData]);

            }

        }else{

            if($request->restaurant > 0){


                $orders = Order::where('restaurant_id', $request->restaurant)->whereDate('created_at', '>=', $request->start." 00:00:00")->whereDate('created_at', '<=', $request->end." 00:00:00")->get();

                $getData = [];
                
                $online = 0;
                $kapida_nakit = 0;
                $kapida_ticket = 0;
                $kapida_k_karti = 0;
                $topsiparis = 0;

                foreach ($orders as $order) {
                    $couriers = CourierOrder::where('order_id', $order->id)->first();

                    if(isset($couriers)){

                        $courier = Courier::where('id', $couriers->courier_id)->first();
                    if(isset($courier)){
                        $cour = $courier->name;
                    }else{
                        $cour = "kim bu";
                    }
                    
                     if($order->payment_method == "Online Kredi/Banka Kartı"){
                                $online += $order->amount;
                        }
                        if($order->payment_method == "Kapıda Nakit ile Ödeme"){
                            $kapida_nakit += $order->amount;
                        }
                        if($order->payment_method == "Kapıda Ticket ile Ödeme"){
                            $kapida_ticket += $order->amount;
                        }
                         if($order->payment_method == "Kapıda Kredi Kartı ile Ödeme"){
                            $kapida_k_karti += $order->amount;
                        }
                         
                        $topsiparis ++;

                    $data = [
                        "platform" => $order->platform,
                        "courier" => $cour,
                        "tracking_id" => $order->tracking_id,
                        "full_name" => $order->full_name,
                        "phone" => $order->phone,
                        "payment" => $order->payment_method,
                        "topsiparis" => $topsiparis, 
                        "amount" => $order->amount." TL",
                        "online" => number_format($online, 2)." TL", 
                        "kapida_nakit" => number_format($kapida_nakit, 2)." TL", 
                        "kapida_ticket" => number_format($kapida_ticket, 2)." TL", 
                        "kapida_k_karti" => number_format($kapida_k_karti, 2)." TL", 
                        "time" => Carbon::parse($order->created_at)->translatedFormat('d-m-Y- H:i'),
                        "message" => $order->message,
                        "message2" => $order->message2
                    ];

                    array_push($getData, $data);

                    }

                    

                }

                return response()->json(['data' => $getData]);

            }else{
                
            }
        }

    }

 

}
