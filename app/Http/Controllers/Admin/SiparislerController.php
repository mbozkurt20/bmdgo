<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Courier;
use App\Models\Order;
use Illuminate\Support\Facades\Hash;


use Carbon\Carbon;
use App\Jobs\AssignOrderToCourier;

class SiparislerController extends Controller
{
    public function deletedOrders()
    {
        $orders = Order::where('status', 'UNSUPPLIED')
            ->whereHas('restaurant', function($query){
                return $query->where('admin_id', auth()->id());
            })
            ->whereDate('created_at', Carbon::today())->orderBy('created_at', 'desc')->get();
        return view('admin.Orders.deletedOrders', compact('orders'));
    }
    public function deliveredOrders()
    {
        $couriers = Courier::where('status', 'active')->where('restaurant_id', Auth::user()->id)->get();
        $orders = Order::where('status', 'DELIVERED')
            ->whereHas('restaurant', function($query){
                return $query->where('admin_id', auth()->id());
            })
            ->whereDate('created_at', Carbon::today())->orderBy('created_at', 'desc')->get();
        return view('admin.Orders.deliveredOrders', compact('orders', 'couriers'));
    }
}
