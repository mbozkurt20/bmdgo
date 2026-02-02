<?php

namespace App\Console\Commands;

use App\Helpers\OrderStatus;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckOrderStatusCommand extends Command
{
    protected $signature = 'orders:check-status';
    protected $description = '5 saniyesi geçen siparişlerin statusunu günceller';

    public function handle()
    {
        $threshold = now()->subSeconds(5);

        $orders = Order::where('status', OrderStatus::PENDING)
            ->where('created_at', '<=', $threshold)
            ->get();

        foreach ($orders as $order) {
            $order->status = OrderStatus::PREPARED;
            $order->save();

            Log::info("Sipariş #{$order->id} status PREPARED yapıldı");
        }
    }
}
