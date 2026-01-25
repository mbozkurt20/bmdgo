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

        // Eager Loading ile ilişkileri çekiyoruz (N+1 problemini önler)
        $query = Order::with(['courierOrder.courier']);

        if ($courierId > 0) {
            $query->whereHas('courierOrder', function($q) use ($courierId) {
                $q->where('courier_id', $courierId);
            });
        }

        if ($restaurantId > 0) {
            $query->where('restaurant_id', $restaurantId);
        }

        $orders = $query->whereBetween('created_at', [$startDate, $endDate])->get();

        // Toplamları doğrudan Collection üzerinden hesaplayalım (Daha hızlı ve hatasız)
        $totals = [
            'online' => $orders->where('payment_method', 'Online Kredi/Banka Kartı')->sum('amount'),
            'kapida_nakit' => $orders->where('payment_method', 'Kapıda Nakit ile Ödeme')->sum('amount'),
            'kapida_ticket' => $orders->where('payment_method', 'Kapıda Ticket ile Ödeme')->sum('amount'),
            'kapida_k_karti' => $orders->where('payment_method', 'Kapıda Kredi Kartı ile Ödeme')->sum('amount'),
            'topsiparis' => $orders->count(),
        ];

        $data = $orders->map(function($order) {
            return [
                "platform" => $order->platform,
                "courier" => $order->courierOrder->courier->name ?? 'Bilinmiyor',
                "tracking_id" => $order->tracking_id,
                "full_name" => $order->full_name,
                "phone" => $order->phone,
                "payment" => $order->payment_method,
                "amount" => number_format($order->amount, 2, '.', ''), // JS'de toplamak için formatlamayı sade tutun
                "time" => Carbon::parse($order->created_at)->translatedFormat('d-m-Y H:i'),
                "distance" => $order->distance,
            ];
        });

        return response()->json([
            'data' => $data,
            'totals' => $totals
        ]);
    }
}
