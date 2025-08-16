<?php

namespace Database\Seeders;

use App\Models\SystemFeature;
use Illuminate\Database\Seeder;

class SystemFeauteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $features = [
            [
                'name' => 'Paket Gelince Bildir',
                'description' => 'Yeni paket geldiğinde kuryeye zil sesi ve uyarı mesajı gönderir.'
            ],
            [
                'name' => 'Paket Otomatik Atansın',
                'description' => 'Yeni gelen paketleri otomatik olarak uygun kuryeye atar.'
            ],
            [
                'name' => 'Kurye Paket Statüleri Bildir',
                'description' => 'Kuryeye teslimat sürecindeki paketlerin durum değişikliklerini bildirir.'
            ],
            [
                'name' => 'Kurye Paket İptal Edebilsin',
                'description' => 'Kuryenin belirli koşullarda paketleri iptal etmesine izin verir.'
            ],
            [
                'name' => 'Kurye Boş Paketleri Görebilsin',
                'description' => 'Kuryenin henüz kimseye atanmış olmayan paketleri görüntülemesini sağlar.'
            ],
        ];

        foreach ($features as $feature) {
            SystemFeature::create($feature);
        }
    }
}
