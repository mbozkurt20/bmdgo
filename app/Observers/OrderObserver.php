<?php

namespace App\Observers;

use App\Helpers\CourierStatus;
use App\Helpers\OrdersHelper;
use App\Helpers\OrderStatus;
use App\Helpers\SendSms;
use App\Jobs\AssignPendingOrders;
use App\Jobs\UpdateOrderStatusJob;
use App\Models\Admin;
use App\Models\Courier;
use App\Models\Order;
use App\Models\Printer;
use App\Models\Restaurant;
use App\Services\OrderStatusService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Pusher\Pusher;

class OrderObserver
{
    public function creating(Order $order)
    {
        OrdersHelper::updateTopup($order->restaurant->admin->id, $order->restaurant_id);
    }

    public function created(Order $order)
    {
        $order->verify_code = OrdersHelper::generateVerifyCode();
        $order->saveQuietly();

        // 5 saniye sonra durumu PENDING'den PREPARED'a değiştir
        UpdateOrderStatusJob::dispatch($order)
            ->delay(now()->addSeconds(5));

// Restoranı bul
        $restaurant = Restaurant::find($order->restaurant_id);

// SMS gönder
        $message = sprintf(
            "Sayın %s, %s numaralı siparişiniz alınmıştır.\n\n%s doğrulama kodu ile siparişinizi teslim alabilirsiniz.",
            $order->full_name,
            $order->tracking_id,
            $order->verify_code
        );

        $newStatus = $order->status;

        $or = new OrderStatusService();
        $or->changeStatus($order, $newStatus);

        if ($order->restaurant->admin->is_sms) {
            SendSms::send($order->phone, $message, $restaurant->admin_id);
        }

        if (Auth::guard('restaurant')->check()) {
            $printers = Printer::where('payable_type', 'restaurant')->where('payable_id', $restaurant->id)->pluck('name')->toArray();
            if (count($printers) > 0) {
               OrdersHelper::nowPrint($order->id,$printers);
            }
        }

        $pusher = new Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'),
            config('broadcasting.connections.pusher.options')
        );

        $pusher->trigger("admin-{$restaurant->admin_id}", "new-order", ['order' => $order]);
        $pusher->trigger("restaurant-{$restaurant->id}", "new-order", ['order' => $order]);
    }

    public function updated(Order $order)
    {
        // 1. Sonsuz döngüyü ve gereksiz tetiklenmeyi önlemek için sadece status değiştiyse devam et
        if (!$order->wasChanged('status')) {
            return;
        }

        Log::info("✅ Order #{$order->id} durumu değişti: {$order->status}");

        // Status değişim mantığını çalıştır
        $or = new OrderStatusService();
        $or->changeStatus($order, $order->status);

        $restaurant = $order->restaurant; // Relation üzerinden erişmek daha performanslıdır

        // 2. HANDOVER (Yola Çıktı) Bildirimi
        if ($order->status == OrderStatus::HANDOVER) {
            if ($restaurant->admin->is_sms) {
                SendSms::send($order->phone, "Sayın {$order->full_name}, {$order->tracking_id} numaralı siparişiniz yola çıkmıştır.", $restaurant->admin_id);
            }
        }

        // 3. PREPARED (Hazır) -> Atama Başlat
        // auto_orders kontrolünü optimize ettik
        if ($order->status == OrderStatus::PREPARED && optional($restaurant->admin)->auto_orders) {
            dispatch(new AssignPendingOrders());
        }

        // 4. DELIVERED (Teslim Edildi) -> Kuryeyi Boşa Çıkar
        if ($order->status == OrderStatus::DELIVERED) {
            if ($restaurant->admin->is_sms) {
                SendSms::send($order->phone, "Sayın {$order->full_name}, siparişiniz teslim edilmiştir.", $restaurant->admin_id);
            }

            if ($order->courier_id != -1) {
                $courier = $order->courier;
                // Sadece kurye meşgulse (service) active çek ki CourierObserver tetiklensin
                if ($courier && $courier->status !== CourierStatus::active) {
                    $courier->update(['status' => CourierStatus::active]);
                }
            }
        }

        // 5. Pusher Bildirimi (Bunu bir Job içine almanız şiddetle önerilir)
        $this->dispatchPusherNotification($order, $restaurant);
    }

    /**
     * Pusher bildirimini gönderir
     */
    protected function dispatchPusherNotification($order, $restaurant)
    {
        try {
            $pusher = new Pusher(
                config('broadcasting.connections.pusher.key'),
                config('broadcasting.connections.pusher.secret'),
                config('broadcasting.connections.pusher.app_id'),
                config('broadcasting.connections.pusher.options')
            );

            $orderData = Order::where('id', $order->id)->with(['restaurant', 'courier'])->first();

            $pusher->trigger("admin-{$restaurant->admin_id}", "update-order", ['order' => $orderData]);
            $pusher->trigger("restaurant-{$restaurant->id}", "update-order", ['order' => $orderData]);
        } catch (\Exception $e) {
            Log::error("Pusher Hatası: " . $e->getMessage());
        }
    }

    /**
     * Handle the Courier "deleted" event.
     *
     * @param \App\Models\Order $order
     * @return void
     */
    public function deleted(Order $order)
    {
        $pusher = new Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'),
            config('broadcasting.connections.pusher.options')
        );

        $order = Order::where('id', $order->id)
            ->with(['restaurant', 'courier'])
            ->first();

        if (Auth::guard('restaurant')->check()) {
            // giriş yapan restoran
            $authRestaurant = Auth::guard('restaurant')->user();

            if ($order->restaurant_id == $authRestaurant->id) {
                // sadece kendi order'ı ise tetikle
                $channel = 'restaurant-' . $authRestaurant->id;
                $pusher->trigger($channel, 'update-order', ['order' => $order]);
            }

        } elseif (Auth::guard('admin')->check()) {
            // giriş yapan admin
            $authAdmin = Auth::guard('admin')->user();

            if ($order->restaurant->admin_id == $authAdmin->id) {
                // siparişin restoranının admini ise tetikle
                $channel = 'admin-' . $authAdmin->id;
                $pusher->trigger($channel, 'update-order', ['order' => $order]);
            }
        }
    }
}
