<?php

namespace App\Helpers;

use App\Events\OrderNotification;
use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Auth;

class OrdersHelper {
    public static function addressReplace($address) {
       if ($address){
           $keyword = "Notes:";
           $position = strpos($address, $keyword);

           if ($position !== false) {
               // "Notes:" kelimesinden önceki kısmı alıyoruz
               $address = substr($address, 0, $position);
           }

           return $address;
       }else{return null;}
    }

    public static function createOrderNotification($order): void
    {
        $restaurantIds = Restaurant::where('admin_id',Auth::guard('admin')->user()->id)->pluck('id');

        if (Order::where('id',$order->id)->whereIn('restaurant_id',$restaurantIds)->exists()){
            event(new OrderNotification($order));
        }
    }
}
