<?php

namespace App\Http\Controllers\Dealer\Report;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Courier;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now());

        $dealerId = Auth::guard('dealer')->id();
        $adminIds = Admin::where('created_by_id', $dealerId)->where('created_by_type','dealer')->pluck('id')->toArray();

        $orders = Order::whereHas('restaurant', function ($query) use($adminIds) {
            return $query->whereIn('admin_id',$adminIds);
        })->whereBetween('created_at', [$startDate, $endDate])->get();

        $totalOrders = $orders->count();
        $totalCouriers = Courier::whereIn('id',$adminIds)->count();
        $totalAdmins = Admin::whereIn('id',$adminIds)->count();
        $totalRestaurants = Restaurant::whereIn('id',$adminIds)->count();
        $totalCustomers = Customer::whereHas('restaurant', function ($query) use($adminIds) {
            return $query->whereIn('admin_id',$adminIds);
        })->count();

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
