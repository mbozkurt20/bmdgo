<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Courier;
use App\Models\CourierOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function orders()
    {
        $orders = Order::where('restaurant_id', Auth::user()->id)->where('status', "!=", 'UNSUPPLIED')
            ->whereDate('created_at', Carbon::today())->orderBy('created_at', 'desc')->get();

        return view('restaurant.reports.orders', compact('orders'));
    }

    public function couriers()
    {
        $couriers = Courier::where('restaurant_id', 0)->get();
        return view('restaurant.reports.couriers', compact('couriers'));
    }
    public function globalFilter(Request $request)
    {
        if ($request->courier > 0) {
            $couriers = CourierOrder::where('courier_id', $request->courier)->get();

            $getData = [];
            $online = 0;
            $kapida_nakit = 0;
            $kapida_ticket = 0;
            $kapida_k_karti = 0;
            $topsiparis = 0;
            foreach ($couriers as $courier) {
                $courierx = Courier::where('id', $courier->courier_id)->first();

                $orders = Order::where('id', $courier->order_id)
                    ->where('restaurant_id', Auth::user()->id)
                    ->where('status', "!=", 'UNSUPPLIED')
                    ->whereDate('created_at', '>=', $request->start . " 00:00:00")->whereDate('created_at', '<=', $request->end . " 00:00:00")
                    ->first();

                if ($orders) {
                    $topsiparis++;

                    if ($orders->payment_method == "Online Kredi/Banka Kartı") {
                        $online += $orders->amount;
                    }
                    if ($orders->payment_method == "Kapıda Nakit ile Ödeme") {
                        $kapida_nakit += $orders->amount;
                    }
                    if ($orders->payment_method == "Kapıda Ticket ile Ödeme") {
                        $kapida_ticket += $orders->amount;
                    }
                    if ($orders->payment_method == "Kapıda Kredi Kartı ile Ödeme") {
                        $kapida_k_karti += $orders->amount;
                    }

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
                        "amount" => $orders->amount . " TL",
                        "online" => number_format($online, 2) . " TL",
                        "kapida_nakit" => number_format($kapida_nakit, 2) . " TL",
                        "kapida_ticket" => number_format($kapida_ticket, 2) . " TL",
                        "kapida_k_karti" => number_format($kapida_k_karti, 2) . " TL",
                        "time" => Carbon::parse($orders->created_at)->format('H:i')
                    ];

                    array_push($getData, $data);
                }
            }

            return response()->json(['data' => $getData]);
        }

        if ($request->courier == 0) {
            $orderss = Order::where('restaurant_id', Auth::user()->id)
                ->where('courier_id', '>=', 0)
                ->where('status', "!=", 'UNSUPPLIED')
                ->whereDate('created_at', '>=', $request->start . " 00:00:00")->whereDate('created_at', '<=', $request->end . " 00:00:00")
                ->get();

            $getData = [];

            $online = 0;
            $kapida_nakit = 0;
            $kapida_ticket = 0;
            $kapida_k_karti = 0;
            $topsiparis = 0;

            foreach ($orderss as $orders) {
                if ($orders) {
                    $courierx = Courier::where('id', $orders->courier_id)->first();

                    if ($courierx) {
                        $cou = $courierx->name;
                    } else {
                        $cou = "Kim Bu";
                    }

                    $topsiparis++;

                    if ($orders->payment_method == "Online Kredi/Banka Kartı") {
                        $online += $orders->amount;
                    }
                    if ($orders->payment_method == "Kapıda Nakit ile Ödeme") {
                        $kapida_nakit += $orders->amount;
                    }
                    if ($orders->payment_method == "Kapıda Ticket ile Ödeme") {
                        $kapida_ticket += $orders->amount;
                    }
                    if ($orders->payment_method == "Kapıda Kredi Kartı ile Ödeme") {
                        $kapida_k_karti += $orders->amount;
                    }

                    $data = [
                        "platform" => $orders->platform,
                        "courier" => $cou,
                        "tracking_id" => $orders->tracking_id,
                        "message" => $orders->message,
                        "message2" => $orders->message2,
                        "full_name" => $orders->full_name,
                        "phone" => $orders->phone,
                        "payment" => $orders->payment_method,
                        "topsiparis" => $topsiparis,
                        "amount" => $orders->amount . " TL",
                        "online" => number_format($online, 2) . " TL",
                        "kapida_nakit" => number_format($kapida_nakit, 2) . " TL",
                        "kapida_ticket" => number_format($kapida_ticket, 2) . " TL",
                        "kapida_k_karti" => number_format($kapida_k_karti, 2) . " TL",
                        "time" => Carbon::parse($orders->created_at)->format('H:i')
                    ];

                    array_push($getData, $data);
                }
            }

            return response()->json(['data' => $getData]);
        }

        if ($request->courier < 0) {

            $couriers = CourierOrder::all();

            $getData = [];

            $online = 0;
            $kapida_nakit = 0;
            $kapida_ticket = 0;
            $kapida_k_karti = 0;
            $topsiparis = 0;

            foreach ($couriers as $courier) {
                $courierx = Courier::where('id', $courier->courier_id)->first();

                $orders = Order::where('id', $courier->order_id)
                    ->where('restaurant_id', Auth::user()->id)
                    ->where('status', "!=", 'UNSUPPLIED')
                    ->whereDate('created_at', '>=', $request->start . " 00:00:00")->whereDate('created_at', '<=', $request->end . " 00:00:00")
                    ->first();

                if ($orders) {
                    $topsiparis++;

                    if ($orders->payment_method == "Online Kredi/Banka Kartı") {
                        $online += $orders->amount;
                    }
                    if ($orders->payment_method == "Kapıda Nakit ile Ödeme") {
                        $kapida_nakit += $orders->amount;
                    }
                    if ($orders->payment_method == "Kapıda Ticket ile Ödeme") {
                        $kapida_ticket += $orders->amount;
                    }
                    if ($orders->payment_method == "Kapıda Kredi Kartı ile Ödeme") {
                        $kapida_k_karti += $orders->amount;
                    }
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
                        "amount" => $orders->amount . " TL",
                        "online" => number_format($online, 2) . " TL",
                        "kapida_nakit" => number_format($kapida_nakit, 2) . " TL",
                        "kapida_ticket" => number_format($kapida_ticket, 2) . " TL",
                        "kapida_k_karti" => number_format($kapida_k_karti, 2) . " TL",
                        "time" => Carbon::parse($orders->created_at)->format('H:i')
                    ];

                    array_push($getData, $data);
                }
            }

            return response()->json(['data' => $getData]);
        }
    }
    public function globalFilterOrder(Request $request)
    {
        // Log request data to check if values are being received
        Log::info("Request platform: " . $request->platform);
        Log::info("Request start date: " . $request->start);
        Log::info("Request end date: " . $request->end);

        $getData = [];  // Initialize $getData before any processing

        if ($request->platform != 0) {

            $orders = Order::where('platform', $request->platform)
                ->where('restaurant_id', Auth::user()->id)
                ->where('status', "!=", 'UNSUPPLIED')
                ->whereDate('created_at', '>=', $request->start . " 00:00:00")
                ->whereDate('created_at', '<=', $request->end . " 00:00:00")
                ->get();

            $online = 0;
            $kapida_nakit = 0;
            $kapida_ticket = 0;
            $kapida_k_karti = 0;
            $topsiparis = 0;

            foreach ($orders as $order) {
                if ($order->payment_method == "Online Kredi/Banka Kartı") {
                    $online += $order->amount;
                }
                if ($order->payment_method == "Kapıda Nakit ile Ödeme") {
                    $kapida_nakit += $order->amount;
                }
                if ($order->payment_method == "Kapıda Ticket ile Ödeme") {
                    $kapida_ticket += $order->amount;
                }
                if ($order->payment_method == "Kapıda Kredi Kartı ile Ödeme") {
                    $kapida_k_karti += $order->amount;
                }

                $topsiparis++;

                $data = [
                    "platform" => $order->platform,
                    "tracking_id" => $order->tracking_id,
                    "full_name" => $order->full_name,
                    "phone" => $order->phone,
                    "payment" => $order->payment_method,
                    "topsiparis" => $topsiparis,
                    "amount" => $order->amount . " TL",
                    "online" => number_format($online, 2) . " TL",
                    "kapida_nakit" => number_format($kapida_nakit, 2) . " TL",
                    "kapida_ticket" => number_format($kapida_ticket, 2) . " TL",
                    "kapida_k_karti" => number_format($kapida_k_karti, 2) . " TL",
                    "time" => Carbon::parse($order->created_at)->format('H:i')
                ];

                array_push($getData, $data);
            }
        } else {
            $orders = Order::where('restaurant_id', Auth::user()->id)
                ->where('status', "!=", 'UNSUPPLIED')
                ->whereDate('created_at', '>=', $request->start . " 00:00:00")
                ->whereDate('created_at', '<=', $request->end . " 00:00:00")
                ->get();

            $online = 0;
            $kapida_nakit = 0;
            $kapida_ticket = 0;
            $kapida_k_karti = 0;
            $topsiparis = 0;

            foreach ($orders as $order) {
                if ($order->payment_method == "Online Kredi/Banka Kartı") {
                    $online += $order->amount;
                }
                if ($order->payment_method == "Kapıda Nakit ile Ödeme") {
                    $kapida_nakit += $order->amount;
                }
                if ($order->payment_method == "Kapıda Ticket ile Ödeme") {
                    $kapida_ticket += $order->amount;
                }
                if ($order->payment_method == "Kapıda Kredi Kartı ile Ödeme") {
                    $kapida_k_karti += $order->amount;
                }

                $topsiparis++;

                $data = [
                    "platform" => $order->platform,
                    "tracking_id" => $order->tracking_id,
                    "full_name" => $order->full_name,
                    "phone" => $order->phone,
                    "payment" => $order->payment_method,
                    "topsiparis" => $topsiparis,
                    "amount" => $order->amount . " TL",
                    "online" => number_format($online, 2) . " TL",
                    "kapida_nakit" => number_format($kapida_nakit, 2) . " TL",
                    "kapida_ticket" => number_format($kapida_ticket, 2) . " TL",
                    "kapida_k_karti" => number_format($kapida_k_karti, 2) . " TL",
                    "time" => Carbon::parse($order->created_at)->format('H:i')
                ];

                array_push($getData, $data);
            }
        }

        return response()->json(['data' => $getData]);  // Always return $getData
    }
}
