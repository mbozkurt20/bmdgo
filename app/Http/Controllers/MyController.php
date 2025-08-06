<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyController extends Controller
{
    public function entegrations()
    {
        $restaurant = Restaurant::find(Auth::user()->id);
        return view('restaurant.profile.entegration', compact('restaurant'));
    }
    public function entegrastion_update(Request $request)
    {
        $restaurant = Restaurant::find(Auth::user()->id);
        $restaurant->yemeksepeti_email = $request->yemeksepeti_email;
        $restaurant->yemeksepeti_password = $request->yemeksepeti_password;
        $restaurant->getir_restaurant_id = $request->getir_restaurant_id;
        $restaurant->getir_app_secret_key = $request->getir_app_secret_key;
        $restaurant->getir_restaurant_secret_key = $request->getir_restaurant_secret_key;
        $restaurant->trendyol_satici_id = $request->trendyol_satici_id;
        $restaurant->trendyol_sube_id = $request->trendyol_sube_id;
        $restaurant->trendyol_api_key = $request->trendyol_api_key;
        $restaurant->trendyol_secret_key = $request->trendyol_secret_key;
        $restaurant->adisyo_api_key = $request->adisyo_api_key;
        $restaurant->adisyo_secret_key = $request->adisyo_secret_key;
        $restaurant->adisyo_consumer_adi = $request->adisyo_consumer_adi;
        $restaurant->save();

        return redirect()->back()->with('message', 'Entegrasyon Güncellenmesi Tamamlandı.');
    }
}
