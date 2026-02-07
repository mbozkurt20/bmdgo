<?php

namespace App\Helpers;

use App\Models\Courier;
use App\Models\Order;
use App\Models\ProgressPaymentRecord;

class CourierHelper
{
    /**
     * Kuryenin alacağı olup olmadığını kontrol eder.
     * @return bool (Alacak varsa true, yoksa veya kurye borçluysa false)
     */
    public static function hasReceivable($courierId)
    {
        $courier = Courier::findOrFail($courierId);

        // 1. Toplam Hakediş Hesapla
        $deliveredOrders = Order::where('courier_id', $courier->id)
            ->where('status', OrderStatus::DELIVERED)
            ->get();

        $totalEarned = 0;
        if ($courier->price_type == 'package') {
            $totalEarned = $deliveredOrders->count() * (float)$courier->price;
        } else {
            $kmPrice = (float)$courier->km_price;
            $fixedPrice = (float)$courier->fixed_price;
            $externalKm = (float)$courier->km_distance_later;

            $distanceTotal = $deliveredOrders->sum(function($o) use ($kmPrice, $externalKm) {
                return max(0, (float)$o->distance - $externalKm) * $kmPrice;
            });

            $totalEarned = $distanceTotal + ($fixedPrice * $deliveredOrders->count());
        }

        // 2. Yapılan Toplam Ödeme
        $totalPaid = ProgressPaymentRecord::where('payable_type', 'courier')
            ->where('payable_id', $courier->id)
            ->sum('amount');


        return (float)$totalEarned > (float)$totalPaid;
    }
}
