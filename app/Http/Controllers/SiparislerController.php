<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Courier;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Jobs\AssignOrderToCourier;

class SiparislerController extends Controller
{
    public function deletedOrders()
    {
        $couriers = Courier::where('status', 'active')->where('restaurant_id', Auth::user()->id)->get();
        $orders = Order::where('restaurant_id', Auth::user()->id)->where('status', 'UNSUPPLIED')
            ->whereDate('created_at', Carbon::today())->orderBy('created_at', 'desc')->get();
        return view('restaurant.orders.deletedOrders', compact('orders', 'couriers'));
    }
    public function deliveredOrders()
    {
        $couriers = Courier::where('status', 'active')->where('restaurant_id', Auth::user()->id)->get();
        $orders = Order::where('restaurant_id', Auth::user()->id)->where('status', 'DELIVERED')
            ->whereDate('created_at', Carbon::today())->orderBy('created_at', 'desc')->get();
        return view('restaurant.orders.deliveredOrders', compact('orders', 'couriers'));
    }
}
