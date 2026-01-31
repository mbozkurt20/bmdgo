<?php

namespace App\Observers;

use App\Models\Courier;
use App\Models\CourierStatusMovement;
use Illuminate\Support\Facades\Log;

class CourierObserver
{
    public function updated(Courier $courier)
    {
        // 1. Sadece status kolonunda bir değişiklik olduysa işlem yap
        if (!$courier->wasChanged('status')) {
            return;
        }

        $oldStatus = $courier->getOriginal('status');
        $newStatus = $courier->status;

        Log::info("Kurye #{$courier->id} statüsü değişti: {$oldStatus} -> {$newStatus}");

        try {
            // 2. Önceki açık hareketi kapat (Eski statü ne kadar sürdü?)
            $lastMovement = CourierStatusMovement::where('courier_id', $courier->id)
                ->whereNull('ended_at')
                ->latest('started_at')
                ->first();

            if ($lastMovement) {
                $now = now();
                $lastMovement->update([
                    'ended_at' => $now,
                    // Carbon cast yapıldığından emin olun (Model içinde protected $casts)
                    'duration_seconds' => $lastMovement->started_at->diffInSeconds($now)
                ]);
            }

            // 3. Yeni statü için kayıt başlat
            // Eğer kuryeye son atanan siparişi bağlamak istersen:
            // $orderId = $courier->orders()->latest()->first()?->id;

            CourierStatusMovement::create([
                'courier_id' => $courier->id,
                'status'     => $newStatus,
                'order_id'   => null, // Burası ihtiyaca göre doldurulabilir
                'started_at' => now(),
            ]);

        } catch (\Exception $e) {
            Log::error("Kurye Hareket Kaydı Hatası: " . $e->getMessage());
        }
    }
}
