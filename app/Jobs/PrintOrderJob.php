<?php

namespace App\Jobs;

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
        $this->printers     = $printers;     // ["Mutfak", "Kasa"]
        $this->restaurantId = $restaurantId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $options = [
            'cluster' => env('PUSHER_APP_CLUSTER'),
            'useTLS'  => true,
        ];

        $pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            $options
        );

        $payload = [
            "restaurant_id" => $this->restaurantId,
            "order"         => $this->order,      // array olarak gönder
            "printers"      => $this->printers,   // ["Mutfak", "Kasa"]
        ];

        foreach ($this->printers as $printerName) {
            // 👇 Kanal formatı: printer-{restaurantId}-{printerName}
            $channel = "printer-{$this->restaurantId}-{$printerName}";

            $pusher->trigger($channel, "print-order", $payload);
        }
    }
}
