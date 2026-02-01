<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\OrderStatus;
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

        if ($request->input('status') === 'cancelled') {
            $query = Order::query()->where('status', OrderStatus::UNSUPPLIED);
        }
        if ($request->input('status') === 'all') {
            $query = Order::query()->where('status', OrderStatus::DELIVERED);
        }
        if ($request->input('status') === 'delivered') {
            $query = Order::query();
        }


        if ($courierId > 0) {
            $orderIds = CourierOrder::where('courier_id', $courierId)->pluck('order_id');
            $query->whereIn('id', $orderIds);
        }

        if ($restaurantId > 0) {
            $query->where('restaurant_id', $restaurantId);
        }

        $orders = $query->whereBetween('created_at', [$startDate, $endDate])->get();

        // Ödeme haritası ve sayaçlar
        $methodMap = [
            "Online Kredi/Banka Kartı" => "online",
            "Kapıda Nakit ile Ödeme" => "nakit",
            "Kapıda Kredi Kartı ile Ödeme" => "kkarti",
            "Kapıda Ticket ile Ödeme" => "ticket",
            "Kapıda Sodexo ile Ödeme" => "sodexo",
            "Kapıda Multinet ile Ödeme" => "multinet",
            "Kapıda Pluxee ile Ödeme" => "pluxee"
        ];

        $totals = [
            'topsiparis' => 0,
            'online' => 0, 'nakit' => 0, 'kkarti' => 0,
            'ticket' => 0, 'sodexo' => 0, 'multinet' => 0, 'pluxee' => 0,
            'topciro' => 0
        ];

        $getData = [];

        foreach ($orders as $order) {
            // Kurye ismi bulma
            $courierOrder = CourierOrder::where('order_id', $order->id)->first();
            $courierName = 'Yönetici Kuryesi';
            if($courierOrder) {
                $c = Courier::find($courierOrder->courier_id);
                $courierName = $c ? $c->name : 'Yönetici Kuryesi';
            }

            // Ödeme toplamını ilgili kategoriye ekle
            $methodKey = $methodMap[$order->payment_method] ?? null;
            if ($methodKey) {
                $totals[$methodKey] += $order->amount;
            }

            $totals['topciro'] += $order->amount;
            $totals['topsiparis']++;

            $getData[] = [
                "platform" => $order->platform,
                "courier" => $courierName,
                "tracking_id" => $order->tracking_id,
                "full_name" => $order->full_name,
                "phone" => $order->phone,
                "payment" => $order->payment_method,
                "amount" => number_format($order->amount, 2, ',', '.') . " TL",
                "time" => Carbon::parse($order->created_at)->translatedFormat('d-m-Y H:i'),
                "distance" => $order->distance ? number_format($order->distance, 0, ',', '.') : '0',
            ];
        }

        return response()->json([
            'data' => $getData,
            'totals' => $totals
        ]);
    }
}
