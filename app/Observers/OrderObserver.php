<?php

namespace App\Observers;

use App\Helpers\OrdersHelper;
use App\Helpers\SendSms;
use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Log;
use Pusher\Pusher;

class OrderObserver
{
    public function creating(Order $order)
    {
       OrdersHelper::updateTopup(null,$order->restaurant_id);
    }

    public function created(Order $order)
    {
        $order->verify_code = OrdersHelper::generateVerifyCode();
        $order->saveQuietly(); // updated event TETİKLENMEZ

        $restaurant = Restaurant::find($order->restaurant_id);

        SendSms::send($order->phone,'Sayın '.$order->full_name.', '. $order->tracking_id. ' numaralı siparişiniz alınmıştır.'. '\n \n '.
            $order->verify_code.' doğrulama kodu ile siparişinizi teslim alabilirsiniz.', $restaurant->admin_id);


        $options = array (
            'cluster' => 'mt1',
            'useTLS' => true
        );

        $pusher = new Pusher (
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            $options
        );

        $pusher->trigger('my-channel', 'orders', ['order' => $order]);
    }

    public function updated(Order $order)
    {
        Log::info('✅ Order updated event tetiklendi', [
            'id' => $order->id,
            'phone' => $order->phone,
            'status' => $order->status
        ]);

        $restaurant = Restaurant::find($order->restaurant_id);

        if ($order->status == 'HANDOVER') {
            SendSms::send($order->phone,'Sayın '.$order->full_name.', '. $order->tracking_id. ' numaralı siparişiniz yola çıkmıştır.', $restaurant->admin_id);
        }

        if ($order->status == 'DELIVERED') {
            SendSms::send($order->phone,'Sayın '.$order->full_name.', '. $order->tracking_id. ' numaralı siparişiniz teslim edilmiştir. \n \n Bizi tercih ettiğiniz için teşekkür ederiz.', $restaurant->admin_id);
        }
    }
}
