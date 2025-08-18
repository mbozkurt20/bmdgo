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
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now());

        $orders = Order::whereBetween('created_at', [$startDate, $endDate])->get();

        $totalOrders = $orders->count();
        $totalCouriers = Courier::count();
        $totalAdmins = Admin::count();
        $totalRestaurants = Restaurant::count();
        $totalCustomers = Customer::count();

        $totalSubAmount = $orders->sum('sub_amount');
        $totalDiscount = $orders->sum('discount');
        $totalAmount = $orders->sum('amount');

        // Orders per day for chart
        $ordersByDate = $orders->groupBy(function($order) {
            return $order->created_at->format('Y-m-d');
        })->map->count();

        return view('superadmin.reports.index', compact(
            'startDate',
            'endDate',
            'totalOrders',
            'totalCouriers',
            'totalAdmins',
            'totalRestaurants',
            'totalCustomers',
            'totalSubAmount',
            'totalDiscount',
            'totalAmount',
            'ordersByDate'
        ));
    }
    public function downloadReport(){

    }
}
