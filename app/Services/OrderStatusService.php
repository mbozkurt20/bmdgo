<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderStatusLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrderStatusService
{
    public function changeStatus(Order $order, string $newStatus)
    {
        DB::transaction(function () use ($order, $newStatus) {
            // 1. Önceki logu bul
            $lastLog = OrderStatusLog::where('order_id', $order->id)
                ->orderByDesc('changed_at')
                ->first();

            if ($lastLog) {
                // Duration hesapla
                $lastLog->update([
                    'duration_seconds' => Carbon::parse($lastLog->changed_at)->diffInSeconds(now()),
                ]);
            }

            // 2. Yeni log ekle
            OrderStatusLog::create([
                'order_id' => $order->id,
                'restaurant_id' => $order->restaurant_id,
                'status' => $newStatus,
                'changed_at' => now(),
            ]);

            // (Opsiyonel) Order tablosuna current status yaz
            $order->update(['status' => $newStatus]);
        });
    }
}
