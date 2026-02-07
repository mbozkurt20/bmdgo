<?php

namespace App\Helpers;

class MapHelper {
    public static function getGoogleDistance($lat1, $lng1, $lat2, $lng2)
    {
        $apiKey = config('site.google_map_api_key');

        // Google Distance Matrix API URL
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins={$lat1},{$lng1}&destinations={$lat2},{$lng2}&mode=driving&key={$apiKey}";

        try {
            $response = file_get_contents($url);
            $data = json_decode($response, true);

            if ($data['status'] === 'OK' && $data['rows'][0]['elements'][0]['status'] === 'OK') {
                // 'value' metre cinsinden mesafe döndürür (Örn: 5000 = 5km)
                $distanceInMeters = $data['rows'][0]['elements'][0]['distance']['value'];

                // Kilometreye çevirip döndürelim
                return round($distanceInMeters / 1000, 2);
            }
        } catch (\Exception $e) {
            // Hata durumunda (API limit, bağlantı vb.) yedek olarak Haversine'e dönebilirsin
            return null;
        }

        return null;
    }
}
