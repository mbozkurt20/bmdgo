<?php

namespace App\Helpers;

use App\Events\OrderNotification;
use App\Models\Admin;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\Topup;
use Illuminate\Support\Facades\Auth;

class OrdersHelper
{
    public static function addressReplace($address)
    {
        if ($address) {
            $keyword = "Notes:";
            $position = strpos($address, $keyword);

            if ($position !== false) {
                // "Notes:" kelimesinden önceki kısmı alıyoruz
                $address = substr($address, 0, $position);
            }

            return $address;
        } else {
            return null;
        }
    }

    public static function createOrderNotification($order): void
    {
        $id = Auth::guard('admin')->user()->id ?? 10;
        $restaurantIds = Restaurant::where('admin_id', $id)->pluck('id');

        if (Order::where('id', $order->id)->whereIn('restaurant_id', $restaurantIds)->exists()) {
            event(new OrderNotification($order));
        }
    }

    public static function generateVerifyCode(): string
    {
        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        if (Order::where('verify_code',$code)->exists()){
            $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        }

        return $code;
    }

    public static function isTopup($adminId, $restaurantId)
    {
        if ($restaurantId && !$adminId) {
            $adminId = Restaurant::find($restaurantId)->admin_id;
        }

        $admin = Admin::find($adminId);

        return $admin->top_up_balance > 0;
    }

    public static function updateTopup($adminId, $restaurantId)
    {
        if ($restaurantId && !$adminId) {
            $adminId = Restaurant::find($restaurantId)->admin_id;
        }

        $admin = Admin::find($adminId);

        if ($admin->top_up_balance > 0) {
            $admin->top_up_balance--;
            $admin->update();

            return true;
        } else {
            Notification::create([
                'title' => 'Yetersiz Kontör Bakiyesi',
                'description' => 'Üzgünüz, Kontor bakiyeniz yetersiz olduğu için ürün eklemesi yapılamıyor!!',
            ]);

            return false;
        }
    }
}
