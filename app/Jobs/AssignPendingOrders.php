<?php

namespace App\Jobs;

use App\Helpers\CourierStatus;
use App\Helpers\MapHelper;
use App\Helpers\NotificationHelper;
use App\Helpers\OrdersHelper;
use App\Helpers\OrderStatus;
use App\Models\CourierOrder;
use App\Models\Customer;
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

        // Atanmamış ve Hazırlanmış siparişleri al
        $orders = Order::where('courier_id', -1)
            ->where('status', OrderStatus::PREPARED)
            ->orderBy('created_at', 'asc')
            ->get();

        if ($orders->isEmpty()) {
            Log::info('Otomatik Atama: Atanacak uygun sipariş bulunamadı.');
            return;
        }

        Log::info("Otomatik Atama: {$orders->count()} adet sipariş inceleniyor.");

        foreach ($orders as $order) {
            // Müsait kurye bul (En uzun süredir atama yapılmayandan başla - Round Robin)
            $courier = Courier::where('status', CourierStatus::active)
                ->where('admin_id', $order->restaurant->admin_id)
                ->orderBy('last_assigned_at', 'asc')
                ->first();

            if (!$courier) {
                Log::warning("Sipariş #{$order->id} (Takip: {$order->tracking_id}) için MÜSAİT KURYE YOK. Döngü sonlandırılıyor.");
                break; // Kurye yoksa diğer siparişler için de yoktur, döngüden çık.
            }

            // --- KOORDİNAT VE MESAFE KONTROLLERİ ---
            $customer = Customer::find($order->customer_id);

            if (!$customer || !$customer->latitude || !$courier->latitude) {
                Log::error("Sipariş #{$order->id} ES GEÇİLDİ: Koordinat eksik. (Kurye Lat: {$courier->latitude}, Müşteri Lat: " . ($customer->latitude ?? 'YOK') . ")");
                continue;
            }

            // Mesafe Hesapla
            $distance = MapHelper::getGoogleDistance(
                $courier->latitude, $courier->longitude,
                $customer->latitude, $customer->longitude
            );

            if ($distance === null) {
                $distance = OrdersHelper::haversineDistance(
                    $courier->latitude, $courier->longitude,
                    $customer->latitude, $customer->longitude
                );
                Log::info("Sipariş #{$order->id}: Google API yanıt vermedi, Haversine ile hesaplandı.");
            }

            $distanceInKm = $distance / 1000;

            // MESAFE SINIRI KONTROLÜ (50 KM)
            if ($distanceInKm > 50) {
                Log::alert("Sipariş #{$order->id} MESAFEDEN DOLAYI ES GEÇİLDİ! Mesafe: " . round($distanceInKm, 2) . " km. Sınır: 50 km.");
                continue; // Bu siparişi atla, bir sonrakine bak.
            }

            // --- ATAMA İŞLEMİ BAŞLIYOR ---
            try {
                $order->courier_id = $courier->id;
                $order->assigned_at = now();
                $order->status = OrderStatus::ASSIGNED;
                $order->update();

                // Kurye zaman aşımı job'ı
                CheckCourierTimeoutJob::dispatch($order->id)->delay(now()->addMinutes(2));

                // Kurye son atama zamanı güncelle
                $courier->last_assigned_at = now();
                $courier->update();

                // İlişki tablosu kaydı
                CourierOrder::firstOrCreate([
                    'courier_id' => $courier->id,
                    'order_id' => $order->id
                ]);

                Log::info("BAŞARILI: Sipariş #{$order->id}, Kurye #{$courier->id} ({$courier->name}) kişisine atandı. Mesafe: " . round($distanceInKm, 2) . " km.");

                // Bildirimler
                $restaurant = Restaurant::find($order->restaurant_id);
                if ($courier->fcm_token) {
                    $ser = new PushNotificationService();
                    $ser->sendNotification(
                        $courier->fcm_token,
                        $restaurant->restaurant_name . ' - Yeni Sipariş',
                        'Takip Kodu: ' . $order->tracking_id
                    );
                    Log::info("Bildirim Gönderildi: Kurye {$courier->name}");
                }

                if (OrdersHelper::getOrderSystem(3)) {
                    NotificationHelper::add([
                        'title' => 'Paket Kuryeye Atandı',
                        'description' => "{$order->tracking_id} nolu paket {$courier->name} kuryesine atandı.",
                        'url' => route('admin.balance')
                    ]);
                }

            } catch (\Exception $e) {
                Log::error("Sipariş #{$order->id} atanırken HATA OLUŞTU: " . $e->getMessage());
            }
        }

        Log::info('--- OTOMATİK KURYE ATAMA DÖNGÜSÜ TAMAMLANDI ---');
    }
}
