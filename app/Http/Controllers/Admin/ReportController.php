<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Courier;
use App\Models\Restaurant;
use App\Models\Order;
use App\Models\CourierOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $couriers = Courier::where('restaurant_id', 0)->where('admin_id', auth()->id())->get();
        $restaurants = Restaurant::where('status', 'active')->where('admin_id', auth()->id())->get();

        return view('admin.reports.index', compact('couriers', 'restaurants'));
    }

    public function globalFilter(Request $request)
    {
        $courierId = $request->courier ?? 0;
        $restaurantId = $request->restaurant ?? 0;
        $startDate = Carbon::parse($request->start)->startOfDay();
        $endDate = Carbon::parse($request->end)->endOfDay();

        $getData = [];
        $online = $kapida_nakit = $kapida_ticket = $kapida_k_karti = 0;
        $topsiparis = 0;

// Sorguyu dinamik olarak oluştur
        $query = Order::query();

        if ($courierId > 0) {
            $orderIds = CourierOrder::where('courier_id', $courierId)->pluck('order_id');
            $query->whereIn('id', $orderIds);
        }

        if ($restaurantId > 0) {
            $query->where('restaurant_id', $restaurantId);
        }

        $query->whereBetween('created_at', [$startDate, $endDate]);

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
                "full_name" => $order->full_name,
                "phone" => $order->phone,
                "payment" => $order->payment_method,
                "amount" => number_format($order->amount, 2) . " TL",
                "topsiparis" => $topsiparis,
                "online" => number_format($online, 2) . " TL",
                "kapida_nakit" => number_format($kapida_nakit, 2) . " TL",
                "kapida_ticket" => number_format($kapida_ticket, 2) . " TL",
                "kapida_k_karti" => number_format($kapida_k_karti, 2) . " TL",
                "time" => Carbon::parse($order->created_at)->translatedFormat('d-m-Y H:i'),
                "distance" => number_format($order->distance, 3),
                "message" => $order->message ?? null,
                "message2" => $order->message2 ?? null,
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
}
