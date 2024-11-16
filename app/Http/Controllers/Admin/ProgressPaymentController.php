<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Courier;
use App\Models\Order;
use App\Models\Restaurant;
use Carbon\Carbon;
use App\Models\CourierOrder;


class ProgressPaymentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function restaurant(){
        $couriers = Courier::where('status','active')->where('admin_id', auth()->id())->where('restaurant_id', 0)->get();
        $restaurants = Restaurant::where('status','active')->where('admin_id', auth()->id())->get();

        return view('admin.progressPayment.restaurant', compact('couriers','restaurants'));
    }

    public function courier(){
        $couriers = Courier::where('status','active')->where('admin_id', auth()->id())->where('restaurant_id', 0)->get();
        $restaurants = Restaurant::where('status','active')->where('admin_id', auth()->id())->get();

        return view('admin.progressPayment.courier', compact('couriers','restaurants'));
    }

    public function restaurantFilter(Request $request){
        $restaurant = Restaurant::find($request->restaurant);
        $startDate = Carbon::createFromFormat('Y-m-d', $request->start)->startOfDay();
        $endDate = Carbon::createFromFormat('Y-m-d', $request->end)->endOfDay();
        $orderCount = Order::where('restaurant_id', $restaurant->id)->whereBetween('created_at', [$startDate, $endDate])->count();
        return response()->json(['restaurant_name' => $restaurant->restaurant_name, "order_count" => $orderCount, "total_progress_payment" => ($restaurant->package_price * $orderCount)]);
    }


    public function courierFilter(Request $request){
        $courier = Courier::find($request->courier);
        $startDate = Carbon::createFromFormat('Y-m-d', $request->start)->startOfDay();
        $endDate = Carbon::createFromFormat('Y-m-d', $request->end)->endOfDay();
        $orderCount = CourierOrder::where('courier_id', $courier->id)->whereBetween('created_at', [$startDate, $endDate])->count();
        return response()->json(['courier_name' => $courier->name, "order_count" => $orderCount, "total_progress_payment" => ($courier->price * $orderCount)]);
    }
 

}
