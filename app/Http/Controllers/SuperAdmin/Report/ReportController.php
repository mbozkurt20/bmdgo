<?php

namespace App\Http\Controllers\SuperAdmin\Report;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Courier;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate   = $request->input('end_date', now()->addDay()->toDateString());
        $groupBy   = $request->input('group_by', 'day'); // day | week

        // 🔹 Siparişler
        $orders = Order::whereBetween('created_at', [$startDate, $endDate])->get();

        // 🔹 Genel toplamlar
        $totalOrders      = $orders->count();
        $totalSubAmount   = $orders->sum('sub_amount');
        $totalDiscount    = $orders->sum('discount');
        $totalAmount      = $orders->sum('amount');

        // 🔹 Diğer modelleri topla (tek sorgudan)
        $couriers    = Courier::whereBetween('created_at', [$startDate, $endDate])->get();
        $admins      = Admin::whereBetween('created_at', [$startDate, $endDate])->get();
        $restaurants = Restaurant::whereBetween('created_at', [$startDate, $endDate])->get();
        $customers   = Customer::whereBetween('created_at', [$startDate, $endDate])->get();

        $totalCouriers    = $couriers->count();
        $totalAdmins      = $admins->count();
        $totalRestaurants = $restaurants->count();
        $totalCustomers   = $customers->count();

        // 🔹 Gruplama fonksiyonu
        $groupFn = function($item) use ($groupBy) {
            if ($groupBy === 'week') {
                return \Carbon\Carbon::parse($item->created_at)->startOfWeek()->format('Y-m-d');
            }
            return \Carbon\Carbon::parse($item->created_at)->format('Y-m-d');
        };

        // 🔹 Dataset fonksiyonu
        $makeDataset = function($collection, $field = null, $sum = false) use ($groupFn) {
            if ($sum) {
                return $collection->groupBy($groupFn)->map(fn($items) => $items->sum($field));
            }
            return $collection->groupBy($groupFn)->map(fn($items) => $items->count());
        };

        // 🔹 Günlük/Haftalık kırılım
        $ordersByDate      = $makeDataset($orders);
        $couriersByDate    = $makeDataset($couriers);
        $adminsByDate      = $makeDataset($admins);
        $restaurantsByDate = $makeDataset($restaurants);
        $customersByDate   = $makeDataset($customers);

        $subAmountByDate = $makeDataset($orders, 'sub_amount', true);
        $discountByDate  = $makeDataset($orders, 'discount', true);
        $amountByDate    = $makeDataset($orders, 'amount', true);

        // 🔹 Boş günleri doldur (eksik gün varsa sıfır yap)
        $period = $groupBy === 'week'
            ? \Carbon\CarbonPeriod::create($startDate, '1 week', $endDate)
            : \Carbon\CarbonPeriod::create($startDate, $endDate);

        $labels = collect($period)->map(fn($d) =>
        $groupBy === 'week' ? $d->startOfWeek()->format('Y-m-d') : $d->format('Y-m-d')
        );

        $fillMissing = function($dataset) use ($labels) {
            return $labels->mapWithKeys(fn($d) => [$d => $dataset[$d] ?? 0]);
        };

        $metrics = [
            ['title' => 'Toplam Sipariş',    'value' => $totalOrders,      'data' => $fillMissing($ordersByDate)],
            ['title' => 'Toplam Kurye',      'value' => $totalCouriers,    'data' => $fillMissing($couriersByDate)],
            ['title' => 'Toplam Admin',      'value' => $totalAdmins,      'data' => $fillMissing($adminsByDate)],
            ['title' => 'Toplam Restoran',   'value' => $totalRestaurants, 'data' => $fillMissing($restaurantsByDate)],
            ['title' => 'Toplam Müşteri',    'value' => $totalCustomers,   'data' => $fillMissing($customersByDate)],
            ['title' => 'Toplam Ara Toplam', 'value' => number_format($totalSubAmount, 2), 'data' => $fillMissing($subAmountByDate)],
            ['title' => 'Toplam İndirim',    'value' => number_format($totalDiscount, 2),  'data' => $fillMissing($discountByDate)],
            ['title' => 'Toplam Net Tutar',  'value' => number_format($totalAmount, 2),    'data' => $fillMissing($amountByDate)],
        ];

        return view('superadmin.reports.index', compact(
            'startDate', 'endDate', 'groupBy', 'metrics'
        ));
    }

    public function downloadReport(){

    }
}
