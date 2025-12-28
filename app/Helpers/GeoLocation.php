<?php

namespace App\Helpers;

class GeoLocation
{
    /* ----------------------------------------------------
     |  PUBLIC API
     |----------------------------------------------------*/

    /**
     * Controller için adres üretici
     */
    public static function buildAddress(array $data): string
    {
        $parts = [];

        // Sokak + bina no (bina no SADECE sayısal ise)
        if (!empty($data['sokak'])) {
            $street = $data['sokak'];

            if (!empty($data['bina_no']) && is_numeric($data['bina_no'])) {
                $street .= ' ' . $data['bina_no'];
            }

            $parts[] = $street;
        }

        // Mahalle
        if (!empty($data['mahalle'])) {
            $parts[] = $data['mahalle'];
        }

        // İlçe
        if (!empty($data['ilce'])) {
            $parts[] = $data['ilce'];
        }

        // İl
        if (!empty($data['il'])) {
            $parts[] = $data['il'];
        }

        $parts[] = 'Türkiye';

        return implode(', ', $parts);
    }

    /**
     * Ana fonksiyon: adres → lat / lng
     */
    public static function getLatLong(string $address): array
    {
        $original = trim($address);
        $cleaned  = self::cleanAddress($original);

        $queries = self::generateFallbackQueries($cleaned);

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

    /* ----------------------------------------------------
     |  CORE LOGIC
     |----------------------------------------------------*/

    /**
     * Google Geocoding API
     */
    private static function queryGoogle(string $query): ?array
    {
        $apiKey = config('services.google.maps_key');

        $url = 'https://maps.googleapis.com/maps/api/geocode/json?' . http_build_query([
                'address'  => $query,
                'key'      => $apiKey,
                'language' => 'tr',
                'region'   => 'tr',
            ]);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 5,
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        if (!$response) {
            return null;
        }

        $data = json_decode($response, true);

        if (($data['status'] ?? '') !== 'OK') {
            return null;
        }

        return $data['results'][0] ?? null;
    }

    /**
     * Fallback adresler üretir
     */
    private static function generateFallbackQueries(string $address): array
    {
        $queries = [];

        // 1️⃣ Tam adres
        $queries[] = $address;

        // 2️⃣ Sokaksız (mahalle + ilçe + il)
        $queries[] = self::stripStreet($address);

        // 3️⃣ İlçe + il
        if (preg_match('/([a-zçğıöşü\s]+),\s*([a-zçğıöşü\s]+),\s*türkiye/i', $address, $m)) {
            $queries[] = "{$m[2]}, Türkiye";
        }

        return array_unique(array_filter($queries));
    }

    /**
     * Sokak bilgisini kırpar
     */
    private static function stripStreet(string $address): string
    {
        $parts = explode(',', $address);
        array_shift($parts); // sokak çıkar
        return trim(implode(',', $parts));
    }

    /**
     * Sonucu tek tip hale getirir
     */
    private static function formatResult(array $data, string $raw, string $used): array
    {
        return [
            'lat' => $data['geometry']['location']['lat'],
            'lon' => $data['geometry']['location']['lng'],
            'display_name' => $data['formatted_address'],
            'used_address' => $used,
            'original_input' => $raw,
            'confidence' => self::confidenceScore($data),
        ];
    }

    /* ----------------------------------------------------
     |  UTILITIES
     |----------------------------------------------------*/

    /**
     * Adres temizleme
     */
    private static function cleanAddress(string $address): string
    {
        $address = mb_strtolower($address);

        $unwanted = [
            'mah.', 'mahallesi', 'mahalle',
            'sok.', 'sokak', 'cad.', 'cadde',
            'apartman', 'apt', 'bina', 'blok',
            'kat', 'daire', 'no:', 'no',
        ];

        $address = str_ireplace($unwanted, '', $address);
        $address = preg_replace('/[^\p{L}\p{N}\s,]/u', '', $address);
        $address = preg_replace('/\s+/', ' ', $address);

        return trim($address);
    }

    /**
     * Basit doğruluk puanı (debug & kalite için)
     */
    private static function confidenceScore(array $data): int
    {
        $score = 0;

        foreach ($data['address_components'] as $component) {
            if (in_array('street_number', $component['types'])) $score += 30;
            if (in_array('route', $component['types'])) $score += 30;
            if (in_array('administrative_area_level_2', $component['types'])) $score += 20;
            if (in_array('locality', $component['types'])) $score += 20;
        }

        return min($score, 100);
    }
}
