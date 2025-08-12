<?php

namespace App\Jobs;

use App\Models\CourierOrder;
use App\Models\Order;
use App\Models\Courier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AssignOrderToCourier implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;
    protected $retryCount;

    public function __construct(Order $order, int $retryCount = 0)
    {
        $this->order = $order;
        $this->retryCount = $retryCount;
    }

    public function handle()
    {
        // Sipariş zaten atanmışsa dur
        if ($this->order->courier_id) {
            return;
        }

        // Müsait kurye bul
        $courier = Courier::where('status', 'active')
            ->where('online', true)
            ->where('admin_id', $this->order->restaurant->admin_id)
            ->first();

        if ($courier) {
            // Atama yap
            $this->order->courier_id = $courier->id;
            $this->order->status = 'assigned';
            $this->order->save();

            CourierOrder::create([

            ]);

            $courier->status = 'busy';
            $courier->save();

            Log::info("Sipariş #{$this->order->id} kurye #{$courier->id} ile eşlendi.");
        } else {
            // Kurye yoksa tekrar dene (maks 5)
            if ($this->retryCount < 5) {
                self::dispatch($this->order, $this->retryCount + 1)
                    ->delay(now()->addMinutes(5));
                Log::info("Kurye bulunamadı, tekrar denenecek. Deneme: {$this->retryCount}");
            }
        }
    }
}
