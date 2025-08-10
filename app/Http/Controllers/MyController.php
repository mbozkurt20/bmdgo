<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Restaurant;
use App\Services\VatanSmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MyController extends Controller
{
    public function entegrations()
    {
        $restaurant = Restaurant::find(Auth::user()->id);
        return view('restaurant.entegrations.platforms', compact('restaurant'));
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

    public function smsEntegrations()
    {
        $admin = Admin::find(Auth::user()->id);
        return view('admin.entegrations.sms', compact('admin'));
    }

    public function smsEntegrastionUpdate(Request $request)
    {
        $restaurant = Admin::find(Auth::user()->id);
        $restaurant->vatan_sms_customer = $request->vatan_sms_customer;
        $restaurant->vatan_sms_username = $request->vatan_sms_username;;
        $restaurant->vatan_sms_password = $request->vatan_sms_password;;
        $restaurant->vatan_sms_orginator = $request->vatan_sms_orginator;;
        $restaurant->save();

        return redirect()->back()->with('message', 'Sms Entegrasyon Güncellenmesi Tamamlandı.');
    }
    public function smsEntegrastionTest(Request $request)
    {
        $auth = Auth::guard('admin')->user();

        if ($auth->vatan_sms_customer && $auth->vatan_sms_username && $auth->vatan_sms_password && $auth->vatan_sms_orginator){

            try {
                $smsService = new VatanSmsService();
                $result = $smsService->sendSms($request->phone, 'Sayın '.$auth->name.', '.' sms bilgileriniz doğrulanmıştır.'. '\n \n '.
                    'Dilerseniz panelinizden "Aktif Et" diyerek sms göndermeyi aktifleştirebilirsiniz.',$auth->id);

                if($result == "2:Kullanici bulunamadi") {
                    return redirect()->back()->with('test', 'Sms Bilgileriniz Hatalı Görünüyor');
                }
                return redirect()->back()->with('message', 'Sms Gönderildi');
            }catch (\Exception $e){
                return redirect()->back()->with('test', $e->getMessage());
            }
        }else{
            return redirect()->back()->with('test', 'Lütfen gerekli tüm bilgileri giriniz!!');
        }
    }
    public function smsEntegrastionStatus()
    {
        $auth = Auth::guard('admin')->user();
        $auth->is_sms = !$auth->is_sms;

        $auth->update();

        echo "OK";
    }

    public function profile()
    {
        return view('restaurant.profile');
    }
    public function profileUpdate(Request $request)
    {
        $auth = Auth::guard('restaurant')->user();

        if ($request->password){
            $auth->password = Hash::make($request->password);
        }

        $auth->latitude = $request->input('latitude');
        $auth->longitude = $request->input('longitude');
        $auth->name = $request->input('name');
        $auth->phone = $request->input('phone');
        $auth->save();

        return redirect()->back()->with('message', 'Bilgileriniz Güncellenmiştir.');
    }
}
