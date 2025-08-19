<?php

namespace App\Observers;

use App\Helpers\CourierStatus;
use App\Helpers\OrdersHelper;
use App\Helpers\OrderStatus;
use App\Helpers\SendSms;
use App\Jobs\AssignOrderToCourier;
use App\Jobs\AssignPendingOrders;
use App\Models\Admin;
use App\Models\Courier;
use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Pusher\Pusher;

class OrderObserver
{
    public function creating(Order $order)
    {
        OrdersHelper::updateTopup(null, $order->restaurant_id);
    }

    public function created(Order $order)
    {
        $order->verify_code = OrdersHelper::generateVerifyCode();
        $order->saveQuietly(); // updated event TETİKLENMEZ

        $restaurant = Restaurant::find($order->restaurant_id);

        SendSms::send($order->phone, 'Sayın ' . $order->full_name . ', ' . $order->tracking_id . ' numaralı siparişiniz alınmıştır.' . '\n \n ' .
            $order->verify_code . ' doğrulama kodu ile siparişinizi teslim alabilirsiniz.', $restaurant->admin_id);

        if (Admin::where('id', $restaurant->admin_id)->first()->auto_orders) {
            if ($order) {
                dispatch(new AssignPendingOrders());
            }
        }

        $options = array(
            'cluster' => 'mt1',
            'useTLS' => true
        );

        $pusher = new Pusher (
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            $options
        );

        $pusher->trigger('orders', 'new-order', ['order' => $order]);
    }

    public function updated(Order $order)
    {
        Log::info('✅ Order updated event tetiklendi', [
            'id' => $order->id,
            'phone' => $order->phone,
            'status' => $order->status
        ]);

        $restaurant = Restaurant::find($order->restaurant_id);

        if ($order->status == OrderStatus::HANDOVER) {
            SendSms::send($order->phone, 'Sayın ' . $order->full_name . ', ' . $order->tracking_id . ' numaralı siparişiniz yola çıkmıştır.', $restaurant->admin_id);
        }

        if ($order->status == OrderStatus::DELIVERED) {
            SendSms::send($order->phone, 'Sayın ' . $order->full_name . ', ' . $order->tracking_id . ' numaralı siparişiniz teslim edilmiştir. \n \n Bizi tercih ettiğiniz için teşekkür ederiz.', $restaurant->admin_id);

            $courier = Courier::find($order->courier_id);
            $courier->status = CourierStatus::active;
            $courier->update();
        }

        $options = array(
            'cluster' => 'mt1',
            'useTLS' => true
        );

        $pusher = new Pusher (
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            $options
        );

        $order = Order::where('id', $order->id)->with(['restaurant','courier'])->first();
        $pusher->trigger('orders', 'update-order', ['order' => $order]);
    }

    /**
     * Handle the Courier "deleted" event.
     *
     * @param \App\Models\Order $order
     * @return void
     */
    public function deleted(Order $order)
    {
        $options = array(
            'cluster' => 'mt1',
            'useTLS' => true
        );

        $pusher = new Pusher (
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            $options
        );

        $order = Order::where('id', $order->id)->with('restaurant')->first();
        $pusher->trigger('orders-' . Auth::user()->id, 'update-orders-' . Auth::user()->id, ['order' => $order]);
    }
}
