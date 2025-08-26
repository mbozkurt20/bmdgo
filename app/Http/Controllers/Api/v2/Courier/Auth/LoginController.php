<?php

namespace App\Http\Controllers\Api\v2\Courier\Auth;

use App\Helpers\CourierStatus;
use App\Helpers\Json;
use App\Http\Controllers\Controller;
use App\Http\Resources\CourierResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Courier;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|numeric',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Courier kullanıcıyı bul
        $courier = Courier::where('phone', $request->phone)->first();

        if (isset($courier->is_active) && !$courier->is_active){
            JWTAuth::invalidate(JWTAuth::getToken());

            return Json::success('Hesabınız Aktif Edilmemiş, yöneticiniz ile iletişime geçiniz.',401);
        }

        if (!$courier || !Hash::check($request->password, $courier->password)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $ttl = 60;  // Token'ın geçerlilik süresi 60 dakika
        $expiryDate = Carbon::now()->addMinutes($ttl);

        $token = JWTAuth::fromUser($courier, ['exp' => $expiryDate]);

        return response()->json(['message' => 'Giriş Başarılı', 'token' => $token, 'expiry_date' => $expiryDate,'courier' => new CourierResource($courier)], 200);
    }

    public function register(Request $request){
        $requestData = Validator::make($request->all(), [
            'name' => 'required',
            'phone' => 'required',
            'password' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'birthday' => 'required',
            'price_type' => 'required',
        ]);

        if ($requestData->fails()) {
            return Json::error($requestData->errors());
        }

        if ($request->input('price_type') == 'package' && (!$request->has('price') || $request->input('price') == null)) {
            return Json::error('Lütfen Paket Başı Ücretinizi Giriniz!!');
        }

        if ($request->input('price_type') === 'fixed') {
            if (empty($request->input('fixed_price')) || empty($request->input('km_price'))) {
                return Json::error('Lütfen Sabit Ücret ve Km Başı Ücretinizi Giriniz');
            }
        }

        if (Courier::where('phone' ,$request->input('phone'))->exists()) {
            return Json::error('Bu telefon numarasına ait bir kayıt zaten mevcut.');
        }

        $courier = Courier::create([
            'name' => $request->input('name'),
            'birthday' => $request->input('birthday'),
            'phone' => $request->input('phone'),
            'password' => Hash::make($request->input('password')),
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'price_type' => $request->input('price_type'),
            'price' => $request->input('price')??0.00,
            'fixed_price' => $request->input('fixed_price'),
            'km_price' => $request->input('km_price'),
            'code' => $this->generateCode(),
            'status' => CourierStatus::passive,
        ]);

        return Json::success('Kaydınız Başarıyla Alınmıştır',$courier);
    }

    public function generateCode()
    {
        $code = rand(100000, 999999);

        if (Courier::where('code', $code)->exists()) {
            $code = rand(100000, 999999);
        }

        return $code;
    }

    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return Json::success('Oturumunuz Sonlandırıldı',201);
        } catch (JWTException $e) {
            return Json::error($e->getMessage());
        }
    }
}
