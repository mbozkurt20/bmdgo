<?php

namespace App\Jobs;

use App\Models\Order;
use App\Helpers\OrderStatus;
use App\Services\GpsYemekOutboundService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateOrderStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function handle()
    {

        // Sipariş hala PENDING mi kontrol et (arada iptal edilmiş olabilir)
        if ($this->order->status === OrderStatus::PENDING) {
            $this->order->update(['status' => OrderStatus::PREPARED]);
            Log::info("Sipariş #{$this->order->id} Job ile PREPARED yapıldı.");

            // GPS Yemek platformu için bildirim gönder
            (new GpsYemekOutboundService())->notifyStatusChange($this->order, OrderStatus::PREPARED);
        }
    }
}
