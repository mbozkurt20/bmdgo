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
        $courierId = $request->courier ?? -1;
        $restaurantId = Auth::user()->id;
        $startDate = $request->start . " 00:00:00";
        $endDate = $request->end . " 23:59:59";

        $getData = [];
        $online = $kapida_nakit = $kapida_ticket = $kapida_k_karti = 0;
        $topsiparis = 0;

        // Order sorgusunu dinamik oluştur
        $query = Order::where('restaurant_id', $restaurantId)
            ->where('status', '!=', 'UNSUPPLIED')
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($courierId > 0) {
            $orderIds = CourierOrder::where('courier_id', $courierId)->pluck('order_id');
            $query->whereIn('id', $orderIds);
        } elseif ($courierId == 0) {
            $query->where('courier_id', '>=', 0);
        }

        $orders = $query->get();

        foreach ($orders as $order) {
            // Kurye bilgisi
            $courierOrder = CourierOrder::where('order_id', $order->id)->first();
            $courierName = $courierOrder ? (Courier::find($courierOrder->courier_id)->name ?? 'Bilinmiyor') : 'Bilinmiyor';

            // Ödeme toplamlarını güncelle
            switch ($order->payment_method) {
                case "Online Kredi/Banka Kartı":
                    $online += $order->amount;
                    break;
                case "Kapıda Nakit ile Ödeme":
                    $kapida_nakit += $order->amount;
                    break;
                case "Kapıda Ticket ile Ödeme":
                    $kapida_ticket += $order->amount;
                    break;
                case "Kapıda Kredi Kartı ile Ödeme":
                    $kapida_k_karti += $order->amount;
                    break;
            }

            $topsiparis++;

            $getData[] = [
                "platform" => $order->platform,
                "courier" => $courierName,
                "tracking_id" => $order->tracking_id,
                "message" => $order->message,
                "message2" => $order->message2,
                "full_name" => $order->full_name,
                "phone" => $order->phone,
                "payment" => $order->payment_method,
                "amount" => number_format($order->amount, 2) . " TL",
                "topsiparis" => $topsiparis,
                "online" => number_format($online, 2) . " TL",
                "kapida_nakit" => number_format($kapida_nakit, 2) . " TL",
                "kapida_ticket" => number_format($kapida_ticket, 2) . " TL",
                "kapida_k_karti" => number_format($kapida_k_karti, 2) . " TL",
                "time" => Carbon::parse($order->created_at)->format('H:i')
            ];
        }

        return response()->json([
            'data' => $getData,
            'totals' => [
                'online' => number_format($online, 2),
                'kapida_nakit' => number_format($kapida_nakit, 2),
                'kapida_ticket' => number_format($kapida_ticket, 2),
                'kapida_k_karti' => number_format($kapida_k_karti, 2),
                'topsiparis' => $topsiparis
            ]
        ]);
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
