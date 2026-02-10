<?php

namespace App\Services;

class EntegraService {

    // Değişken yerine sabit (const) kullanmak statik metodlar için en temiz yoldur.
    private const BASE_URL = 'https://entegra.gpsyazilim.com/api/v1';

    public static function newBusiness($payload)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => self::BASE_URL . '/business',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response);
    }

    public static function newRestaurant($payload)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => self::BASE_URL . '/restaurant',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response);
    }

    public static function patchProvider($restaurant, $platform)
    {
        $providerMap = [
            'yemeksepeti' => 1,
            'trendyol'    => 2,
            'getir'       => 3,
            'migros'      => 4,
        ];

        if (!isset($providerMap[$platform])) {
            throw new \Exception('Geçersiz platform');
        }

        $providerId = $providerMap[$platform];
        $fet = json_decode($restaurant->$platform);

        $payload = [
            'status'        => (bool) ($fet->status ?? false),
            'otomatikOnay'  => (bool) ($fet->otomatikOnay ?? false),
            'information'   => $fet->information ?? [],
            'service'       => $fet->service ?? null,
            'doNotKnock'    => $fet->doNotKnock ?? null,
            'dropOffAtDoor' => $fet->dropOffAtDoor ?? null,
            'isEcoFriendly' => $fet->isEcoFriendly ?? null,
        ];

        $curl = curl_init();

        curl_setopt_array($curl, [
            // Statik erişim: self::BASE_URL
            CURLOPT_URL => self::BASE_URL . "/restaurant/{$restaurant->entegra_restaurant_id}/provider/{$providerId}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
        ]);

        $response = curl_exec($curl);

        if ($response === false) {
            throw new \Exception(curl_error($curl));
        }

        curl_close($curl);

        return json_decode($response, true);
    }

    public static function updateOrder($orderId){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => self::BASE_URL . "/order/{$orderId}/update-status",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_POSTFIELDS => json_encode([]),
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response);
    }

    public static function rejectOrderStatuses($orderId){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => self::BASE_URL . "/order/{$orderId}/reject-statuses",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode([]),

        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response);
    }

    public static function rejectOrder($orderId, $payload){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => self::BASE_URL . "/order/{$orderId}/reject",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response);
    }
}
