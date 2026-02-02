<?php

namespace App\Http\Controllers;

use App\Helpers\OrderStatus;
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
        $orders = Order::where('restaurant_id', Auth::user()->id)->where('status', OrderStatus::DELIVERED)
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
        $restaurantId = Auth::id();
        $startDate = $request->start . " 00:00:00";
        $endDate = $request->end . " 23:59:59";

        // Modelindeki paymentMethods() statik fonksiyonunu kullanarak harita oluşturuyoruz
        $methods = Order::paymentMethods();
        $methodMap = [
            $methods['online']      => "online",
            $methods['nakit']       => "kapida_nakit",
            $methods['kredi_karti'] => "kapida_k_karti",
            $methods['ticket']      => "kapida_ticket",
            $methods['sodexo']      => "kapida_ticket",
            $methods['multinet']    => "kapida_ticket",
            $methods['pluxee']      => "kapida_ticket",
        ];

        // Sorgu: Modelindeki 'courier' ilişkisini önceden yüklüyoruz (Eager Loading)
        $query = Order::with('courier')
            ->where('restaurant_id', $restaurantId)
            ->whereBetween('created_at', [$startDate, $endDate])
            // Status Filtresi: cancelled gelirse iptaller, gelmezse teslim edilenler
            ->when($request->status == 'cancelled',
                fn($q) => $q->where('status', \App\Helpers\OrderStatus::UNSUPPLIED),
                fn($q) => $q->where('status', \App\Helpers\OrderStatus::DELIVERED)
            );

        // Kurye Filtresi (Senin modelindeki courier_id üzerinden)
        if ($courierId > 0) {
            $query->where('courier_id', $courierId);
        } elseif ($courierId == 0) {
            $query->whereNotNull('courier_id');
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        $getData = [];
        $totals = [
            'online' => 0, 'kapida_nakit' => 0, 'kapida_ticket' => 0,
            'kapida_k_karti' => 0, 'topsiparis' => 0
        ];

        foreach ($orders as $order) {
            $key = $methodMap[$order->payment_method] ?? null;
            if ($key) {
                $totals[$key] += (float)$order->amount;
            }

            $totals['topsiparis']++;

            $getData[] = [
                "platform"    => strtoupper($order->platform),
                // Modelindeki 'courier' ilişkisini kullanıyoruz
                "courier"     => $order->courier->name ?? 'Kurye Yok',
                "tracking_id" => $order->tracking_id,
                "full_name"   => $order->full_name,
                "phone"       => $order->phone,
                "payment"     => $order->payment_method,
                "amount"      => number_format($order->amount, 2) . " ₺",
                "time"        => \Carbon\Carbon::parse($order->created_at)->format('H:i')
            ];
        }

        // Toplamları formatla
        $formattedTotals = collect($totals)->map(function($val, $key) {
            return $key == 'topsiparis' ? $val : number_format($val, 2);
        });

        return response()->json([
            'data'   => $getData,
            'totals' => $formattedTotals
        ]);
    }

    public function globalFilterOrder(Request $request)
    {
        $start = $request->start . " 00:00:00";
        $end = $request->end . " 23:59:59";

        $methodMap = [
            "Online Kredi/Banka Kartı" => "online",
            "Kapıda Nakit ile Ödeme" => "nakit",
            "Kapıda Kredi Kartı ile Ödeme" => "kkarti",
            "Kapıda Ticket ile Ödeme" => "ticket",
            "Kapıda Sodexo ile Ödeme" => "sodexo",
            "Kapıda Multinet ile Ödeme" => "multinet",
            "Kapıda Pluxee ile Ödeme" => "pluxee"
        ];

        $orders = Order::where('restaurant_id', Auth::id())
            ->whereBetween('created_at', [$start, $end])
            // İptal filtresi: Eğer request'ten iptal gelirse sadece onları, gelmezse sadece teslim edilenleri getir
            ->when($request->status == 'cancelled',
                fn($q) => $q->where('status', OrderStatus::UNSUPPLIED),
                fn($q) => $q->where('status', OrderStatus::DELIVERED)
            )
            ->when($request->platform != "0", fn($q) => $q->where('platform', $request->platform))
            ->get();

        $totals = array_fill_keys(array_values($methodMap), 0);
        $totals['count'] = $orders->count();
        $totals['grand_total'] = $orders->sum('amount'); // Genel Toplam

        $data = $orders->map(function ($order) use ($methodMap, &$totals) {
            $key = $methodMap[$order->payment_method] ?? null;
            if ($key) $totals[$key] += $order->amount;

            return [
                "platform" => strtoupper($order->platform),
                "tracking_id" => $order->tracking_id,
                "full_name" => $order->full_name,
                "payment" => $order->payment_method,
                "amount" => number_format($order->amount, 2) . " TL",
                "time" => \Carbon\Carbon::parse($order->created_at)->format('H:i')
            ];
        });

        return response()->json(['data' => $data, 'totals' => $totals]);
    }
}
