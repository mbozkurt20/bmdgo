<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeoLocation
{
    /**
     * Ana fonksiyon: Adres -> Lat/Lng
     */
    public static function getLatLong(string $address): array
    {
        $original = trim($address);
        // Google temizlenmiş adresten ziyade net yapılandırılmış adresi daha iyi anlar.
        // Bu yüzden temizleme işlemini çok agresif yapmamak bazen daha iyidir.
        $queries = self::generateFallbackQueries($original);

        foreach ($queries as $query) {
            $result = self::queryGoogle($query);
            if ($result) {
                return self::formatResult($result, $original, $query);
            }
        }

        return [
            'error' => 'Konum bulunamadı',
            'input' => $original,
        ];
    }

    /**
     * Google Geocoding API - Laravel HTTP Client Kullanımı
     */
    private static function queryGoogle(string $query): ?array
    {
        $apiKey = config('services.google.maps_key');

        if (!$apiKey) {
            Log::error('Google Maps API Key eksik!');
            return null;
        }

        $params = [
            'address' => $query,
            'key'     => $apiKey,
            'language' => 'tr',
            'region'   => 'tr',
        ];
        $components = ['country:TR'];
        if (!empty($details['city'])) $components[] = 'administrative_area:' . $details['city'];
        if (!empty($details['district'])) $components[] = 'locality:' . $details['district'];
        $params['components'] = implode('|', $components);

        try {
            $response = Http::timeout(5)->get('https://maps.googleapis.com/maps/api/geocode/json', $params);

            if ($response->successful()) {
                $data = $response->json();
                if (($data['status'] ?? '') === 'OK') {
                    return $data['results'][0];
                }
            }
        } catch (\Exception $e) {
            Log::warning("Google Geocode hatası: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Fallback mekanizması: Adresi parçalayarak daraltır
     */
    private static function generateFallbackQueries(string $address): array
    {
        $queries = [$address];
        $parts = array_map('trim', explode(',', $address));

        // Adım adım adresi kısalt (Sokak -> Mahalle -> İlçe -> İl)
        while (count($parts) > 2) {
            array_shift($parts);
            $queries[] = implode(', ', $parts);
        }

        return array_unique($queries);
    }

    private static function formatResult(array $data, string $raw, string $used): array
    {
        return [
            'lat'            => $data['geometry']['location']['lat'],
            'lon'            => $data['geometry']['location']['lng'],
            'display_name'   => $data['formatted_address'],
            'used_address'   => $used,
            'original_input' => $raw,
            'type'           => $data['geometry']['location_type'], // ROOFTOP, APPROXIMATE vb.
        ];
    }
}
