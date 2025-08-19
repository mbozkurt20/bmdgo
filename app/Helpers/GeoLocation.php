<?php

namespace App\Helpers;

class GeoLocation
{
    /**
     * Adres sadeleştirme – karmaşık ifadeleri temizler
     */
    public static function cleanAddress($address) {
        $address = mb_strtolower($address);

        // Temizlenecek ifadeler
        $unwanted = [
            'mahallesi', 'mah.', 'mahalle', 'no:', 'no', 'blok', 'bina',
            'apt', 'apartman', 'daire', 'kat', 'sok.', 'sokak',
            'cad.', 'cadde', 'bulvar', 'bulv', 'üzeri', 'arkası',
            'karşısı', 'önü', 'yanı', 'yolu', 'site', 'sitesi',
            'go petrol'
        ];

        $address = str_ireplace($unwanted, '', $address);

        // Fazla noktalama ve boşlukları temizle
        $address = preg_replace('/[^\p{L}\p{N}\s,\/]/u', '', $address);
        $address = preg_replace('/\s+/', ' ', $address);
        $address = trim($address);

        // Şehir/ilçe ayrımını düzelt
        $address = str_replace('/', ' ', $address);

        // Türkiye eklensin
        if (!str_contains($address, 'türkiye')) {
            $address .= ', Türkiye';
        }

        return $address;
    }

    /**
     * Adresi Nominatim ile sorgular
     */
    public static function queryNominatim($query) {
        $url = "https://nominatim.openstreetmap.org/search?q=" . urlencode($query) . "&format=json&limit=1";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'MyGeocoder/1.0');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);

        return $data[0] ?? null;
    }

    /**
     * Ana fonksiyon: adres → lat-long (kademeli)
     */
    public static function getLatLong($address)
    {
        $original = trim($address);
        $cleaned = self::cleanAddress($original);

        // 1. Tam temizlenmiş adresi dene
        $result = self::queryNominatim($cleaned);
        if ($result) {
            return self::formatResult($result, $original, $cleaned);
        }

        // 2. Şehir + İlçe varsa sadeleştirip dene
        preg_match('/\b([a-zçğıöşü\s]+)\s+(şanlıurfa|gaziantep|diyarbakır|ankara|istanbul|izmir)\b/u', $cleaned, $matches);
        if ($matches) {
            $partial = $matches[0] . ', Türkiye';
            $result = self::queryNominatim($partial);
            if ($result) {
                return self::formatResult($result, $original, $partial);
            }
        }

        // 3. Sadece şehir + ülke ile dene
        if (str_contains($cleaned, 'şanlıurfa')) {
            $result = self::queryNominatim('şanlıurfa, türkiye');
            if ($result) {
                return self::formatResult($result, $original, 'şanlıurfa, türkiye');
            }
        }

        return ['error' => 'Konum bulunamadı', 'input' => $original];
    }

    /**
     * Sonucu standart formata getir
     */
    private static function formatResult($data, $raw, $used) {
        return [
            'lat' => $data['lat'],
            'lon' => $data['lon'],
            'display_name' => $data['display_name'],
            'used_address' => $used,
            'original_input' => $raw,
        ];
    }
}

