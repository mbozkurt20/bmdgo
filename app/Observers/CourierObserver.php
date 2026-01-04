<?php

namespace App\Observers;

use App\Models\Courier;
use App\Models\CourierStatusMovement;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class CourierObserver
{
    /**
     * Handle the Courier "created" event.
     *
     * @param  \App\Models\Courier  $courier
     * @return void
     */
    public function created(Courier $courier)
    {

    }

    /**
     * Handle the Courier "updated" event.
     *
     * @param  \App\Models\Courier  $courier
     * @return void
     */
    public function updated(Courier $courier)
    {
        Log::info('coureir geldi');
        // 🔒 Sadece status değiştiyse devam et
        if (!$courier->wasChanged('status')) {
            return;
        }

        $newStatus = $courier->status;
        $orderId = null;

        // 🔚 Önceki açık hareket varsa, kapat
        $lastMovement = CourierStatusMovement::where('courier_id', $courier->id)
            ->whereNull('ended_at')
            ->latest('started_at')
            ->first();

        if ($lastMovement) {
            $lastMovement->ended_at = now();
            $lastMovement->duration_seconds = $lastMovement->started_at->diffInSeconds(now());
            $lastMovement->save();
        }

        // 🆕 Yeni hareketi oluştur (order_id varsa eklenir)
        CourierStatusMovement::create([
            'courier_id' => $courier->id,
            'status' => $newStatus,
            'order_id' => $orderId,
            'started_at' => now(),
            'ended_at' => null,
            'duration_seconds' => null,
        ]);
    }

    /**
     * Handle the Courier "deleted" event.
     *
     * @param  \App\Models\Courier  $courier
     * @return void
     */
    public function deleted(Courier $courier)
    {
        //
    }

    /**
     * Handle the Courier "restored" event.
     *
     * @param  \App\Models\Courier  $courier
     * @return void
     */
    public function restored(Courier $courier)
    {
        //
    }

    /**
     * Handle the Courier "force deleted" event.
     *
     * @param  \App\Models\Courier  $courier
     * @return void
     */
    public function forceDeleted(Courier $courier)
    {
        //
    }
}
