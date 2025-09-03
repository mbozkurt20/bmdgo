<?php

namespace App\Jobs;

use App\Helpers\OrdersHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Pusher\Pusher;

class PrintOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $order;
    public $printers;
    public $restaurantId;

    /**
     * Create a new job instance.
     */
    public function __construct($order, $printers, $restaurantId)
    {
        $this->order        = $order;
        $this->printers     = $printers;
        $this->restaurantId = $restaurantId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Eğer worker yanlış kuyruğu dinliyorsa, bu job'u atla
        if ($this->queue !== 'restaurant_' . $this->restaurantId) {
            return;
        }

        $pusher = new Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'),
            [
                'cluster' => config('broadcasting.connections.pusher.options.cluster'),
                'useTLS'  => true,
            ]
        );

        foreach ($this->printers as $printer) {
            $pusher->trigger(
                "printer-{$printer}",   // kanal adı
                "print-order",          // event adı
                [
                    'order'     => OrdersHelper::getOrderData($this->order->id),
                    'printer'   => $printer,
                    'restaurant'=> $this->restaurantId,
                ]
            );
        }
    }
}
