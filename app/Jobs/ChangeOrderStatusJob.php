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

class ChangeOrderStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $orderId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $order = Order::find($this->orderId);

        if (!$order) {
            return;
        }

        // Hâlâ uygunsa değiştir
        if ($order->status === OrderStatus::PENDING) {
            $order->status = OrderStatus::PREPARED;
            $order->save();
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
    }
}
