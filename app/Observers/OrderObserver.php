<?php

namespace App\Observers;

use App\Helpers\OrdersHelper;
use App\Helpers\SendSms;
use App\Models\Notification;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

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

        SendSms::send($order->phone,'Sayın '.$order->full_name.', '. $order->tracking_id. ' numaralı siparişiniz alınmıştır.'. '\n \n '.
            $order->verify_code.' doğrulama kodu ile siparişinizi teslim alabilirsiniz.');
    }

    public function updated(Order $order)
    {
        Log::info('✅ Order updated event tetiklendi', [
            'id' => $order->id,
            'phone' => $order->phone,
            'status' => $order->status
        ]);

        if ($order->status == 'HANDOVER') {
            SendSms::send($order->phone,'Sayın '.$order->full_name.', '. $order->tracking_id. ' numaralı siparişiniz yola çıkmıştır.');
        }

        if ($order->status == 'DELIVERED') {
            SendSms::send($order->phone,'Sayın '.$order->full_name.', '. $order->tracking_id. ' numaralı siparişiniz teslim edilmiştir. \n \n Bizi tercih ettiğiniz için teşekkür ederiz.');
        }
    }
}
