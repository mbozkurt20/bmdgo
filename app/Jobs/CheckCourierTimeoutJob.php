<?php

namespace App\Jobs;

use App\Helpers\OrderStatus;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckCourierTimeoutJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $orderId;

    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
        Log::debug("CheckCourierTimeoutJob start-". $orderId);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $order = Order::find($this->orderId);

        if (!$order) return;

        if ($order->status == OrderStatus::ASSIGNED && $order->assigned_at && now()->diffInSeconds($order->assigned_at) >= 120) {

            Log::info('Courier timeout for order ID: ' . $order->id, ['assigned_at' => $order->assigned_at]);
            $order->update([
                'courier_id' => -1,
                'assigned_at' => null,
                'status' => OrderStatus::PREPARED,
            ]);
        }
    }
}
