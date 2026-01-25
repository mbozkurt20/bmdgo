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
        $topCiro = 0; // Toplam ciro için yeni değişken
        $topsiparis = 0;

        $query = Order::query();

        if ($courierId > 0) {
            $orderIds = CourierOrder::where('courier_id', $courierId)->pluck('order_id');
            $query->whereIn('id', $orderIds);
        }

        if ($restaurantId > 0) {
            $query->where('restaurant_id', $restaurantId);
        }

        $query->whereBetween('created_at', [$startDate, $endDate]);

// Performans için: İlişkileri tek seferde çek (Eager Loading)
        $orders = $query->with('courierOrder.courier')->get();

        foreach ($orders as $order) {
            // Kurye adını ilişkiler üzerinden al (veya eski yöntemi kullanacaksanız kalsın)
            $courierName = $order->courierOrder->courier->name ?? 'Bilinmiyor';

            // Ödeme toplamlarını ve genel ciroyu güncelle
            $currentAmount = (float)$order->amount;
            $topCiro += $currentAmount; // Her siparişi toplam ciroya ekle

            switch ($order->payment_method) {
                case "Online Kredi/Banka Kartı":
                    $online += $currentAmount;
                    break;
                case "Kapıda Nakit ile Ödeme":
                    $kapida_nakit += $currentAmount;
                    break;
                case "Kapıda Ticket ile Ödeme":
                    $kapida_ticket += $currentAmount;
                    break;
                case "Kapıda Kredi Kartı ile Ödeme":
                    $kapida_k_karti += $currentAmount;
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
                "amount" => number_format($currentAmount, 2) . " TL",
                "time" => Carbon::parse($order->created_at)->translatedFormat('d-m-Y H:i'),
                "distance" => number_format($order->distance, 3) . " mt",
            ];
        }

        return response()->json([
            'data' => $getData,
            'totals' => [
                'online' => number_format($online, 2),
                'kapida_nakit' => number_format($kapida_nakit, 2),
                'kapida_ticket' => number_format($kapida_ticket, 2),
                'kapida_k_karti' => number_format($kapida_k_karti, 2),
                'topsiparis' => $topsiparis,
                'top_ciro' => number_format($topCiro, 2) // Frontend'de "Top. Ciro" kısmına bunu bağlayın
            ]
        ]);
    }
}
