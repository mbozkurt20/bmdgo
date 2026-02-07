<?php

namespace App\Jobs;

use App\Helpers\CourierStatus;
use App\Helpers\MapHelper;
use App\Helpers\NotificationHelper;
use App\Helpers\OrdersHelper;
use App\Helpers\OrderStatus;
use App\Models\CourierOrder;
use App\Models\Order;
use App\Models\Courier;
use App\Models\Restaurant;
use App\Services\PushNotificationService;
use Carbon\Carbon;
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
        Log::info('--- OTOMATİK KURYE ATAMA DÖNGÜSÜ BAŞLADI ---');

        $orders = Order::where('courier_id', -1)
            ->where('status', OrderStatus::PREPARED)
            ->orderBy('created_at', 'asc')
            ->whereDate('created_at', Carbon::today())
            ->get();

        if ($orders->isEmpty()) {
            Log::info('Otomatik Atama: Atanacak uygun sipariş bulunamadı.');
            return;
        }

        foreach ($orders as $order) {
            $restaurant = Restaurant::find($order->restaurant_id);
            $distLimit = $restaurant->distance_limit_km ?? 50; // Restoran sınırı yoksa default 50km
            $maxPackageLimit = $restaurant->max_package_limit ?? 4; // Restoran sınırı yoksa default 50km

            // Müsait kurye bul
            $courier = Courier::where('status', CourierStatus::active)
                ->where('admin_id', $order->restaurant->admin_id)
                ->orderBy('last_assigned_at', 'asc')
                ->first();

            if (!$courier) {
                Log::warning("Sipariş #{$order->id} için MÜSAİT KURYE YOK.");
                break;
            }

            // --- YARIÇAP KONTROLÜ (Kurye Restorana Ne Kadar Uzak?) ---
            // Kurye restoranın hizmet alanı içinde mi?
            $distanceToRest = MapHelper::getGoogleDistance(
                $courier->latitude, $courier->longitude,
                $restaurant->latitude, $restaurant->longitude
            ) ?? OrdersHelper::haversineDistance($courier->latitude, $courier->longitude, $restaurant->latitude, $restaurant->longitude);

            $distToRestKm = $distanceToRest / 1000;

            if ($distToRestKm > $distLimit) {
                Log::info("Kurye #{$courier->id} restoranın {$distLimit}km yarıçapı dışında ({$distToRestKm}km). Atama yapılmadı.");
                continue;
            }

            // --- ATAMA İŞLEMİ ---
            try {
                $order->courier_id = $courier->id;
                $order->assigned_at = now();
                $order->update();

                $courier->last_assigned_at = now();
                $courier->update();

                CourierOrder::firstOrCreate(['courier_id' => $courier->id, 'order_id' => $order->id]);

                // --- 4 PAKET KONTROLÜ VE YOLA ÇIKTI (HANDOVER) MANTIĞI ---
                $activePackagesCount = Order::where('courier_id', $courier->id)
                    ->count();

                if ($activePackagesCount >= $maxPackageLimit) {
                    // Kuryeyi meşgul yap ki yeni paket gelmesin
                    $courier->status = CourierStatus::service;
                    $courier->update();

                    Log::info("Kurye #{$courier->id} için 4 paket doldu. Durum: HANDOVER ve BUSY yapıldı.");
                }

                // Bildirim ve Job İşlemleri
                CheckCourierTimeoutJob::dispatch($order->id)->delay(now()->addMinutes(2));

                if ($courier->fcm_token) {
                    $notificationService = new PushNotificationService();
                    $notificationService->sendNotification($courier->fcm_token, $restaurant->restaurant_name . ' - Yeni Sipariş', 'Takip: ' . $order->tracking_id);
                }

                Log::info("BAŞARILI: Sipariş #{$order->id}, Kurye #{$courier->id} kişisine atandı.");

            } catch (\Exception $e) {
                Log::error("Atama Hatası: " . $e->getMessage());
            }
        }

        Log::info('--- OTOMATİK KURYE ATAMA DÖNGÜSÜ TAMAMLANDI ---');
    }
}
