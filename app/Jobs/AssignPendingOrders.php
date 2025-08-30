<?php

namespace App\Jobs;

use App\Helpers\CourierStatus;
use App\Helpers\NotificationHelper;
use App\Helpers\OrdersHelper;
use App\Helpers\OrderStatus;
use App\Http\Controllers\Auth\Restaurant;
use App\Models\CourierOrder;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Courier;
use App\Services\PushNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AssignPendingOrders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @throws \Exception
     */
    public function handle()
    {
        // Atanmamış siparişleri sırayla al
        $orders = Order::where('courier_id', -1)
            ->where('status', OrderStatus::PREPARED)
            ->orderBy('created_at')
            ->get();

        foreach ($orders as $order) {
            // Müsait kurye bul
            $courier = Courier::where('status', CourierStatus::active)
                ->where('online', true)
                ->orderBy('last_assigned_at', 'asc')
                ->where('admin_id', $order->restaurant->admin_id)   // round robin için
                ->first();


            if ($courier) {
                // Siparişi kuryeye ata
                $order->courier_id = $courier->id;
                $order->status = OrderStatus::HANDOVER;
                $order->save();

                // Kuryeyi busy yap ve son atama zamanını güncelle
                $courier->status = CourierStatus::service;
                $courier->last_assigned_at = now();
                $courier->save();

                $restaurant = Restaurant::find($order->restaurant_id);

                //mobil bildiri
                if ($courier->fcm_token){
                    $ser = new PushNotificationService();
                    $ser->sendNotification($courier->fcm_token,$restaurant->restaurant_name.' Restorandan 1 Yeni Siparişiniz Var','Sipariş Takip Kodu:'. $order->tracking_id);
                }

                $orderCourier = CourierOrder::where('courier_id',$courier->id)->where('order_id', $order->id)->first();

                if (!$orderCourier) {
                    // Yeni siparişi kuryeye atama
                    $newOrderCourier = new CourierOrder();
                    $newOrderCourier->courier_id = $courier->id;
                    $newOrderCourier->order_id = $order->id;
                    $newOrderCourier->save();

                    Log::info("Kurye atandı ve durumu Serviste yapıldı. Sipariş ID: " . $order->id . " Kurye ID: " . $courier->id);
                }

                if (OrdersHelper::getOrderSystem(3)){
                    NotificationHelper::add([
                        'title' => 'Paket Kuryeye Atandı',
                        'description' => $order->tracking_id. ' takip numaralı paket '.$courier->name. ' isimli kuryeye atandı.',
                        'url' => route('admin.balance')
                    ]);
                }

                Log::info("Sipariş #{$order->id} kurye #{$courier->id} ile eşlendi.");
            } else {
                Log::info("Sipariş #{$order->id} için müsait kurye yok.");
            }
        }
    }
}
