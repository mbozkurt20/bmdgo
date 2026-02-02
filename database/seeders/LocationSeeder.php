<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('locations')->insert([
            [
                'id' => 1,
                'courier_id' => 2,
                'latitude' => '41.031271',
                'longitude' => '40.5100625',
                'status' => 1,
                'created_at' => '2025-05-12 11:35:54',
                'updated_at' => '2025-05-12 14:35:54',
            ],
            [
                'id' => 2,
                'courier_id' => -1,
                'latitude' => '41.0312565',
                'longitude' => '40.5100783',
                'status' => 1,
                'created_at' => '2025-07-10 17:49:39',
                'updated_at' => '2025-07-10 20:49:39',
            ],
            [
                'id' => 3,
                'courier_id' => 10,
                'latitude' => '41.0312848',
                'longitude' => '40.5100547',
                'status' => 1,
                'created_at' => '2025-05-13 10:48:02',
                'updated_at' => '2025-05-13 13:48:02',
            ],
            [
                'id' => 4,
                'courier_id' => 11,
                'latitude' => '41.0310917',
                'longitude' => '40.5100581',
                'status' => 1,
                'created_at' => '2025-05-13 09:25:21',
                'updated_at' => '2025-05-13 12:25:21',
            ],
            [
                'id' => 5,
                'courier_id' => 12,
                'latitude' => '41.0310693',
                'longitude' => '40.5099236',
                'status' => 1,
                'created_at' => '2025-05-13 09:46:57',
                'updated_at' => '2025-05-13 12:46:57',
            ],
            [
                'id' => 6,
                'courier_id' => 13,
                'latitude' => '41.0307897',
                'longitude' => '40.5111597',
                'status' => 1,
                'created_at' => '2025-05-13 11:18:49',
                'updated_at' => '2025-05-13 14:18:49',
            ],
            [
                'id' => 7,
                'courier_id' => 14,
                'latitude' => '41.0310553',
                'longitude' => '40.5100405',
                'status' => 1,
                'created_at' => '2025-05-13 15:09:55',
                'updated_at' => '2025-05-13 18:09:55',
            ],
            [
                'id' => 8,
                'courier_id' => 16,
                'latitude' => '41.0131187',
                'longitude' => '40.5716415',
                'status' => 1,
                'created_at' => '2025-08-01 15:03:53',
                'updated_at' => '2025-08-01 18:03:53',
            ],
            [
                'id' => 9,
                'courier_id' => 16,
                'latitude' => '41.0312037',
                'longitude' => '40.5101091',
                'status' => 1,
                'created_at' => '2025-05-13 17:30:30',
                'updated_at' => '2025-05-13 17:30:30',
            ],
            [
                'id' => 10,
                'courier_id' => 17,
                'latitude' => '41.0312954',
                'longitude' => '40.5100282',
                'status' => 1,
                'created_at' => '2025-07-10 17:45:58',
                'updated_at' => '2025-07-10 20:45:58',
            ],
            [
                'id' => 11,
                'courier_id' => 19,
                'latitude' => '41.0312108',
                'longitude' => '40.5100041',
                'status' => 1,
                'created_at' => '2025-07-11 12:38:00',
                'updated_at' => '2025-07-11 15:38:00',
            ],
            [
                'id' => 12,
                'courier_id' => 20,
                'latitude' => '41.0209218',
                'longitude' => '40.5286033',
                'status' => 1,
                'created_at' => '2025-07-31 19:20:34',
                'updated_at' => '2025-07-31 22:20:34',
            ],
        ]);
    }
}
