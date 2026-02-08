<?php

namespace App\Jobs;

namespace App\Jobs;

use App\Helpers\CourierStatus;
use App\Helpers\MapHelper;
use App\Helpers\OrdersHelper;
use App\Helpers\OrderStatus;
use App\Models\CourierOrder;
use App\Models\Order;
use App\Models\Courier;
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

    public function handle()
    {
        Log::info('--- OTOMATİK KURYE ATAMA DÖNGÜSÜ BAŞLADI ---');

        // Eager loading (with) kullanarak her döngüde veritabanına gitmeyi engelliyoruz
        $orders = Order::with('restaurant.admin')
            ->where('courier_id', -1)
            ->where('status', OrderStatus::PREPARED)
            ->whereDate('created_at', Carbon::today())
            ->orderBy('created_at', 'asc')
            ->get();

        if ($orders->isEmpty()) {
            Log::info('Otomatik Atama: Atanacak uygun sipariş bulunamadı.');
            return;
        }

        foreach ($orders as $order) {
            $restaurant = $order->restaurant;

            if (!$restaurant) continue;

            $distLimit = $restaurant->distance_limit_km ?? 50;
            $maxPackageLimit = $restaurant->max_package_limit ?? 4;

            // KRİTİK DEĞİŞİKLİK: Sadece tek bir kurye değil, o adminin TÜM aktif kuryelerini alıyoruz.
            // Çünkü en eski kurye uzaktaysa, daha yakındaki bir kuryeye şans vermeliyiz.
            $availableCouriers = Courier::where('status', CourierStatus::active)
                ->where('admin_id', $restaurant->admin_id)
                ->orderBy('last_assigned_at', 'asc')
                ->get();

            if ($availableCouriers->isEmpty()) {
                Log::warning("Sipariş #{$order->id} için şu an HİÇ MÜSAİT KURYE YOK. Diğer siparişlere bakılıyor.");
                continue; // BREAK YERİNE CONTINUE: Bu sipariş için kurye yoksa, belki diğeri için vardır.
            }

            $assignedCourier = null;

            foreach ($availableCouriers as $courier) {
                // Mesafe Kontrolü
                $distanceToRest = MapHelper::getGoogleDistance(
                    $courier->latitude, $courier->longitude,
                    $restaurant->latitude, $restaurant->longitude
                ) ?? OrdersHelper::haversineDistance($courier->latitude, $courier->longitude, $restaurant->latitude, $restaurant->longitude);

                $distToRestKm = $distanceToRest / 1000;

                if ($distToRestKm <= $distLimit) {
                    $assignedCourier = $courier;
                    break; // Uygun kuryeyi bulduk, iç döngüden çık.
                }

                Log::info("Kurye #{$courier->id} mesafe dışındaydı, sonraki kurye deneniyor.");
            }

            if (!$assignedCourier) {
                Log::warning("Sipariş #{$order->id} için uygun mesafede kurye bulunamadı.");
                continue;
            }

            // --- ATAMA İŞLEMİ ---
            try {
                $order->update([
                    'courier_id' => $assignedCourier->id,
                    'assigned_at' => now()
                ]);

                $assignedCourier->update(['last_assigned_at' => now()]);

                CourierOrder::firstOrCreate([
                    'courier_id' => $assignedCourier->id,
                    'order_id' => $order->id
                ]);

                // Paket limit kontrolü
                $activePackagesCount = Order::where('courier_id', $assignedCourier->id)
                    ->whereNotIn('status', [OrderStatus::DELIVERED, OrderStatus::UNSUPPLIED])
                    ->count();

                if ($activePackagesCount >= $maxPackageLimit) {
                    $assignedCourier->update(['status' => CourierStatus::service]);
                    Log::info("Kurye #{$assignedCourier->id} paket limiti doldu: BUSY yapıldı.");
                }

                // Bildirimler
                CheckCourierTimeoutJob::dispatch($order->id)->delay(now()->addMinutes(2));

                if ($assignedCourier->fcm_token) {
                    (new PushNotificationService())->sendNotification(
                        $assignedCourier->fcm_token,
                        $restaurant->restaurant_name . ' - Yeni Sipariş',
                        'Takip: ' . $order->tracking_id
                    );
                }

                Log::info("BAŞARILI: Sipariş #{$order->id}, Kurye #{$assignedCourier->id} kişisine atandı.");

            } catch (\Exception $e) {
                Log::error("Atama Hatası (Sipariş #{$order->id}): " . $e->getMessage());
            }
        }

        Log::info('--- OTOMATİK KURYE ATAMA DÖNGÜSÜ TAMAMLANDI ---');
    }
}
