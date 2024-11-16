<?php

namespace App\Http\Controllers\SuperAdmin\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Order;
use App\Models\Restaurant;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AjaxController extends Controller
{

    public function getDashboardData(Request $request)
    {
        $startDate = Carbon::parse($request->input('start_date'));
        $endDate = Carbon::parse($request->input('end_date'));
        $admin = Admin::where('id', '!=', 1)->get();
        $dealerOrderCount = [];
        foreach ($admin as $row) {
            $restaurantIds = Restaurant::where('admin_id', $row->id)->pluck('id')->toArray();
            $query = Order::whereIn('restaurant_id', $restaurantIds);
            if ($request->has('start_date') && !empty($request->input('start_date'))) {
                $query->where('created_at', '>=', \Carbon\Carbon::parse($startDate)->startOfDay());
            }
            if ($request->has('end_date') && !empty($request->input('end_date'))) {
                $query->where('created_at', '<=', \Carbon\Carbon::parse($endDate)->endOfDay());
            }
            array_push($dealerOrderCount, ["title" => $row->name, 'orderCount' => $query->count()]);
        }

        $ordersByPlatform = Order::selectRaw('platform, COUNT(*) as order_count')
            ->groupBy('platform');
        if ($request->has('start_date') && !empty($request->input('start_date'))) {
            $ordersByPlatform->where('created_at', '>=', \Carbon\Carbon::parse($startDate)->startOfDay());
        }
        if ($request->has('end_date') && !empty($request->input('end_date'))) {
            $ordersByPlatform->where('created_at', '<=', \Carbon\Carbon::parse($endDate)->endOfDay());
        }
        $ordersByPlatform = $ordersByPlatform->get()->toArray();

        $ordersByStatus = Order::selectRaw('status, COUNT(*) as order_count')
            ->groupBy('status');
        if ($request->has('start_date') && !empty($request->input('start_date'))) {
            $ordersByStatus->where('created_at', '>=', \Carbon\Carbon::parse($startDate)->startOfDay());
        }
        if ($request->has('end_date') && !empty($request->input('end_date'))) {
            $ordersByStatus->where('created_at', '<=', \Carbon\Carbon::parse($endDate)->endOfDay());
        }
        $ordersByStatus = $ordersByStatus->get()->toArray();

        return [
            "dealerStatus" => $dealerOrderCount,
            "orderProvider" => $ordersByPlatform,
            "orderStatus" => $ordersByStatus
        ];
    }
}
