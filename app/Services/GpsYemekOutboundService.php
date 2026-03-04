<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GpsYemekOutboundService
{
    const GPS_YEMEK_URL = 'https://gpsyemek.com/api/v1/webhook/orders';

    /**
     * Sipariş durumu değiştiğinde GPS Yemek'i bilgilendirir.
     */
    public function notifyStatusChange(Order $order, string $status): void
    {
        if ($order->platform !== 'gpsyemek') {
            return;
        }

        $apiKey = optional($order->restaurant)->gpsyemek_api_key;

        if (blank($apiKey) || blank($order->tracking_id)) {
            return;
        }

        try {
            Http::withToken($apiKey)
                ->timeout(5)
                ->post(self::GPS_YEMEK_URL, [
                    'event'      => 'order_updated',
                    'order_code' => $order->tracking_id,
                    'status'     => $status,
                ]);
        } catch (\Throwable $e) {
            Log::error('GPS Yemek outbound webhook failed', [
                'order_id'   => $order->id,
                'tracking_id' => $order->tracking_id,
                'status'     => $status,
                'error'      => $e->getMessage(),
            ]);
        }
    }
}
