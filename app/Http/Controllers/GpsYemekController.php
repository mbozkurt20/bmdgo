<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Support\Facades\Http;

class GpsYemekController extends Controller
{
    function index()
    {
        $restaurants = Restaurant::get();
        foreach ($restaurants as $restaurant) {
            $this->orders($restaurant);
        }
    }

    private function orders($restaurant){
        $api_token = 'LN1pHodUUlaXKE7dBJdBI9s5EEorFHFtfMfFB6bz';

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$api_token
        ])->post('https://gpsyemek.com/api/v1/webhook/orders', [
            'event' => 'get_orders'
        ]);

        $orders = $response->json()['orders'] ?? [];

        dd($orders);
    }
}
