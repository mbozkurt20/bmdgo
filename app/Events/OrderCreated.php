<?php

namespace App\Events;


use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class OrderCreated implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public $orderHtml;

    public function __construct( $orderHtml)
    {
        Log::info('orderHtml \n \n'. $orderHtml);
        $this->orderHtml = $orderHtml;
    }

    // public channel (kolay setup). istersen PrivateChannel kullan.
    public function broadcastOn()
    {
        return new Channel('orders');
    }

    // okunması kolay event adı
    public function broadcastAs()
    {
        return 'order.created';
    }

    // açıkça hangi veriyi yollayacağımızı belirtiyoruz
    public function broadcastWith()
    {
        return ['orderHtml' => $this->orderHtml];
    }
}
