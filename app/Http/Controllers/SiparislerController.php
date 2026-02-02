<?php

namespace App\Http\Controllers;

use App\Helpers\CourierStatus;
use App\Helpers\OrderStatus;
use App\Models\Courier;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SiparislerController extends Controller
{
    public function deletedOrders()
    {
        $couriers = Courier::where('status', CourierStatus::active)->where('restaurant_id', Auth::user()->id)->get();
        $orders = Order::where('restaurant_id', Auth::user()->id)->where('status', OrderStatus::UNSUPPLIED)
            ->whereDate('created_at', Carbon::today())->orderBy('created_at', 'desc')->get();
        return view('restaurant.orders.deletedOrders', compact('orders', 'couriers'));
    }
    public function deliveredOrders()
    {
        $couriers = Courier::where('status', CourierStatus::active)->where('restaurant_id', Auth::user()->id)->get();
        $orders = Order::where('restaurant_id', Auth::user()->id)->where('status', OrderStatus::DELIVERED)
            ->whereDate('created_at', Carbon::today())->orderBy('created_at', 'desc')->get();
        return view('restaurant.orders.deliveredOrders', compact('orders', 'couriers'));
    }
}
