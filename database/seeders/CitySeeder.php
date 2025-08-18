<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cities_with_coords = [
            ['id' => 1, 'name' => 'Adana',           'lat' => 36.98615,  'lng' => 35.32531],  // from Adana-specific source :contentReference[oaicite:1]{index=1}
            ['id' => 2, 'name' => 'Adıyaman',        'lat' => 37.7648,   'lng' => 38.2797],   // approx based on region
            ['id' => 3, 'name' => 'Afyonkarahisar',  'lat' => 38.7500,   'lng' => 30.5500],   // approx
            ['id' => 4, 'name' => 'Ağrı',            'lat' => 39.7191,   'lng' => 43.0513],   // approximate, sourced generically
            ['id' => 5, 'name' => 'Aksaray',         'lat' => 38.365349, 'lng' => 34.036914],// :contentReference[oaicite:2]{index=2}
            ['id' => 6, 'name' => 'Amasya',          'lat' => 40.649055, 'lng' => 35.8353],  // lat from GitHub, long from gist :contentReference[oaicite:3]{index=3}
            ['id' => 7, 'name' => 'Ankara',          'lat' => 39.9208,   'lng' => 32.8541],   // :contentReference[oaicite:4]{index=4}
            ['id' => 8, 'name' => 'Antalya',         'lat' => 36.8841,   'lng' => 30.7056],   // :contentReference[oaicite:5]{index=5}
            ['id' => 9, 'name' => 'Ardahan',         'lat' => 41.1105,   'lng' => 42.7022],   // :contentReference[oaicite:6]{index=6}
            ['id' => 10,'name' => 'Artvin',          'lat' => 41.183241, 'lng' => 41.818072], // :contentReference[oaicite:7]{index=7}
            ['id' => 11,'name' => 'Aydın',           'lat' => 37.856965, 'lng' => 27.84102],  // :contentReference[oaicite:8]{index=8}
            ['id' => 12,'name' => 'Balıkesir',       'lat' => 39.649055, 'lng' => 27.881532], // :contentReference[oaicite:9]{index=9}
            ['id' => 13,'name' => 'Bartın',          'lat' => 41.641233, 'lng' => 32.343038], // :contentReference[oaicite:10]{index=10}
            ['id' => 14,'name' => 'Batman',          'lat' => 37.880273, 'lng' => 41.137567], // :contentReference[oaicite:11]{index=11}
            ['id' => 15,'name' => 'Bayburt',         'lat' => 40.255425, 'lng' => 40.224526], // :contentReference[oaicite:12]{index=12}
            ['id' => 16,'name' => 'Bilecik',         'lat' => 40.150013, 'lng' => 29.982694], // :contentReference[oaicite:13]{index=13}
            ['id' => 17,'name' => 'Bingöl',          'lat' => 38.884619, 'lng' => 40.49661],  // :contentReference[oaicite:14]{index=14}
            ['id' => 18,'name' => 'Bitlis',          'lat' => 38.403863, 'lng' => 42.108429], // :contentReference[oaicite:15]{index=15}
            ['id' => 19,'name' => 'Bolu',            'lat' => 40.740494, 'lng' => 31.611391], // :contentReference[oaicite:16]{index=16}
            ['id' => 20,'name' => 'Burdur',          'lat' => 37.72728,  'lng' => 30.289255], // :contentReference[oaicite:17]{index=17}
            ['id' => 21,'name' => 'Bursa',           'lat' => 40.182873, 'lng' => 29.066893], // :contentReference[oaicite:18]{index=18}
            ['id' => 22,'name' => 'Çanakkale',       'lat' => 40.154999, 'lng' => 26.413484], // :contentReference[oaicite:19]{index=19}
            ['id' => 23,'name' => 'Çankırı',         'lat' => 40.601832, 'lng' => 33.613503], // :contentReference[oaicite:20]{index=20}
            ['id' => 24,'name' => 'Çorum',           'lat' => 40.551113, 'lng' => 34.956041], // :contentReference[oaicite:21]{index=21}
            ['id' => 25,'name' => 'Denizli',         'lat' => 37.7756,   'lng' => 29.08826],  // :contentReference[oaicite:22]{index=22}
            ['id' => 26,'name' => 'Diyarbakır',      'lat' => 37.914951, 'lng' => 40.228397], // :contentReference[oaicite:23]{index=23}
            ['id' => 27,'name' => 'Düzce',           'lat' => 40.843165, 'lng' => 31.156342], // :contentReference[oaicite:24]{index=24}
            ['id' => 28,'name' => 'Edirne',          'lat' => 41.68163,  'lng' => 26.56077],  // :contentReference[oaicite:25]{index=25}
            ['id' => 29,'name' => 'Elazığ',          'lat' => 38.680686, 'lng' => 39.226581], // :contentReference[oaicite:26]{index=26}
            ['id' => 30,'name' => 'Erzincan',        'lat' => 39.750226, 'lng' => 39.01634],  // :contentReference[oaicite:27]{index=27}
            ['id' => 31,'name' => 'Erzurum',         'lat' => 39.900255, 'lng' => 41.271463], // :contentReference[oaicite:28]{index=28}
            ['id' => 32,'name' => 'Eskişehir',       'lat' => 39.77688,  'lng' => 30.519916], // :contentReference[oaicite:29]{index=29}
            ['id' => 33,'name' => 'Gaziantep',       'lat' => 37.065862, 'lng' => 37.384706], // :contentReference[oaicite:30]{index=30}
            ['id' => 34,'name' => 'Giresun',         'lat' => 40.912475, 'lng' => 38.390985], // :contentReference[oaicite:31]{index=31}
            ['id' => 35,'name' => 'Gümüşhane',       'lat' => 40.440676, 'lng' => 39.508158], // :contentReference[oaicite:32]{index=32}
            ['id' => 36,'name' => 'Hakkari',         'lat' => 37.583222, 'lng' => 43.733628], // :contentReference[oaicite:33]{index=33}
            ['id' => 37,'name' => 'Hatay',           'lat' => 36.200512, 'lng' => 36.166941], // :contentReference[oaicite:34]{index=34}
            ['id' => 38,'name' => 'Iğdır',           'lat' => 39.918689, 'lng' => 44.066059], // :contentReference[oaicite:35]{index=35}
            ['id' => 39,'name' => 'Isparta',         'lat' => 37.764473, 'lng' => 30.55533],  // :contentReference[oaicite:36]{index=36}
            ['id' => 40,'name' => 'İstanbul',        'lat' => 41.002703, 'lng' => 28.987013],// :contentReference[oaicite:37]{index=37}
            ['id' => 41,'name' => 'İzmir',           'lat' => 38.421318, 'lng' => 27.125037], // :contentReference[oaicite:38]{index=38}
            ['id' => 42,'name' => 'Kahramanmaraş',   'lat' => 38.61848,  'lng' => 27.430698], // :contentReference[oaicite:39]{index=39}
            ['id' => 43,'name' => 'Karabük',         'lat' => 41.203456, 'lng' => 32.622425], // :contentReference[oaicite:40]{index=40}
            ['id' => 44,'name' => 'Karaman',         'lat' => 37.174543, 'lng' => 33.229128], // :contentReference[oaicite:41]{index=41}
            ['id' => 45,'name' => 'Kars',            'lat' => 40.606003, 'lng' => 43.100884], // :contentReference[oaicite:42]{index=42}
            ['id' => 46,'name' => 'Kastamonu',       'lat' => 41.388401, 'lng' => 33.782246], // :contentReference[oaicite:43]{index=43}
            ['id' => 47,'name' => 'Kayseri',         'lat' => 38.734804, 'lng' => 35.48014],  // :contentReference[oaicite:44]{index=44}
            ['id' => 48,'name' => 'Kırıkkale',       'lat' => 39.847822, 'lng' => 33.513056], // :contentReference[oaicite:45]{index=45}
            ['id' => 49,'name' => 'Kırklareli',      'lat' => 41.733661, 'lng' => 27.216027], // :contentReference[oaicite:46]{index=46}
            ['id' => 50,'name' => 'Kırşehir',        'lat' => 39.142044, 'lng' => 34.171205], // :contentReference[oaicite:47]{index=47}
            ['id' => 51,'name' => 'Kilis',           'lat' => 36.718522, 'lng' => 37.120374], // :contentReference[oaicite:48]{index=48}
            ['id' => 52,'name' => 'Kocaeli',         'lat' => 40.855371, 'lng' => 29.890639], // :contentReference[oaicite:49]{index=49}
            ['id' => 53,'name' => 'Konya',           'lat' => 37.864012, 'lng' => 32.479499], // :contentReference[oaicite:50]{index=50}
            ['id' => 54,'name' => 'Kütahya',         'lat' => 39.416568, 'lng' => 29.983354], // :contentReference[oaicite:51]{index=51}
            ['id' => 55,'name' => 'Malatya',         'lat' => 38.35458,  'lng' => 38.312656], // :contentReference[oaicite:52]{index=52}
            ['id' => 56,'name' => 'Manisa',          'lat' => 38.621162, 'lng' => 27.428638], // :contentReference[oaicite:53]{index=53}
            ['id' => 57,'name' => 'Mardin',          'lat' => 37.320755, 'lng' => 40.726267], // :contentReference[oaicite:54]{index=54}
            ['id' => 58,'name' => 'Mersin',          'lat' => 36.536123, 'lng' => 33.792291], // “İçel” = Mersin :contentReference[oaicite:55]{index=55}
            ['id' => 59,'name' => 'Muğla',           'lat' => 37.215702, 'lng' => 28.362725], // :contentReference[oaicite:56]{index=56}
            ['id' => 60,'name' => 'Muş',             'lat' => 38.743641, 'lng' => 41.50512],  // :contentReference[oaicite:57]{index=57}
            ['id' => 61,'name' => 'Nevşehir',        'lat' => 38.626527, 'lng' => 34.711939], // :contentReference[oaicite:58]{index=58}
            ['id' => 62,'name' => 'Niğde',           'lat' => 37.966125, 'lng' => 34.682756], // :contentReference[oaicite:59]{index=59}
            ['id' => 63,'name' => 'Ordu',            'lat' => 40.984563, 'lng' => 37.878696], // :contentReference[oaicite:60]{index=60}
            ['id' => 64,'name' => 'Osmaniye',        'lat' => 37.069697, 'lng' => 36.252085], // :contentReference[oaicite:61]{index=61}
            ['id' => 65,'name' => 'Rize',            'lat' => 41.021355, 'lng' => 40.523655], // :contentReference[oaicite:62]{index=62}
            ['id' => 66,'name' => 'Sakarya',         'lat' => 40.776642, 'lng' => 30.405985], // :contentReference[oaicite:63]{index=63}
            ['id' => 67,'name' => 'Samsun',          'lat' => 41.291738, 'lng' => 36.331684], // :contentReference[oaicite:64]{index=64}
            ['id' => 68,'name' => 'Siirt',           'lat' => 37.933096, 'lng' => 41.949182], // :contentReference[oaicite:65]{index=65}
            ['id' => 69,'name' => 'Sinop',           'lat' => 42.022263, 'lng' => 35.152027], // :contentReference[oaicite:66]{index=66}
            ['id' => 70,'name' => 'Sivas',           'lat' => 39.747322, 'lng' => 37.020389], // :contentReference[oaicite:67]{index=67}
            ['id' => 71,'name' => 'Şanlıurfa',       'lat' => 37.155939, 'lng' => 38.795368], // :contentReference[oaicite:68]{index=68}
            ['id' => 72,'name' => 'Şırnak',          'lat' => 37.518985, 'lng' => 42.460587], // :contentReference[oaicite:69]{index=69}
            ['id' => 73,'name' => 'Tekirdağ',        'lat' => 40.984563, 'lng' => 27.517902], // :contentReference[oaicite:70]{index=70}
            ['id' => 74,'name' => 'Tokat',           'lat' => 40.316708, 'lng' => 36.550775], // :contentReference[oaicite:71]{index=71}
            ['id' => 75,'name' => 'Trabzon',         'lat' => 40.999593, 'lng' => 39.716846], // :contentReference[oaicite:72]{index=72}
            ['id' => 76,'name' => 'Tunceli',         'lat' => 39.105421, 'lng' => 39.545777], // :contentReference[oaicite:73]{index=73}
            ['id' => 77,'name' => 'Uşak',            'lat' => 38.681222, 'lng' => 29.407945], // :contentReference[oaicite:74]{index=74}
            ['id' => 78,'name' => 'Van',             'lat' => 38.48907,  'lng' => 43.402759], // :contentReference[oaicite:75]{index=75}
            ['id' => 79,'name' => 'Yalova',          'lat' => 40.65069,  'lng' => 29.26478],  // :contentReference[oaicite:76]{index=76}
            ['id' => 80,'name' => 'Yozgat',          'lat' => 39.819876, 'lng' => 34.811871], // :contentReference[oaicite:77]{index=77}
            ['id' => 81,'name' => 'Zonguldak',       'lat' => 41.455336, 'lng' => 31.799532], // :contentReference[oaicite:78]{index=78}
        ];

        DB::table('cities')->insert($cities_with_coords);
    }
}
