<?php

namespace App\Http\Controllers\Api\v2\Courier\Profile;

use App\Helpers\CourierStatus;
use App\Helpers\Json;
use App\Http\Controllers\Controller;
use App\Http\Resources\CourierResource;
use App\Models\Courier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class IndexController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courier = auth('courier')->user();

        if (!$courier) {
            return Json::error('Kurye Bulunamadı', 404);
        }

        return Json::success('Kurye Bilgileri', new CourierResource($courier));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function updateLocation(Request $request)
    {
        $courier = auth('courier')->user();

        $requestData = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        if ($requestData->fails()) {
            return Json::error($requestData->errors());
        }

        $courier->latitude = $request->latitude;
        $courier->longitude = $request->longitude;
        $courier->save();

        return Json::success('Konum Güncellendi', new CourierResource($courier));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        if ($request->input('price_type') == 'package' && (!$request->has('price') || $request->input('price') == null)) {
            return Json::error('Lütfen Paket Başı Ücretinizi Giriniz!!');
        }

        if ($request->input('price_type') === 'fixed') {
            if (empty($request->input('fixed_price')) || empty($request->input('km_price'))) {
                return Json::error('Lütfen Sabit Ücret ve Km Başı Ücretinizi Giriniz');
            }
        }

        $id = auth('courier')->id();

        if (Courier::where('phone', $request->input('phone'))
            ->where('id', '!=', $id)
            ->exists()) {
            return Json::error('Bu telefon numarasına ait bir kayıt zaten mevcut.');
        }

        $courier = Courier::find($id);

        $courier->update([
            'name' => $request->input('name') ?? $courier->name,
            'phone' => $request->input('phone') ?? $courier->phone,
            'fcm_token' => $request->input('fcm_token') ?? $courier->fcm_token,
            'birthday' => $request->input('birthday') ?? $courier->birthday,
            'latitude' => $request->input('latitude') ?? $courier->latitude,
            'longitude' => $request->input('longitude') ?? $courier->longitude,
            'price_type' => $request->input('price_type') ?? $courier->price_type,
            'price' => $request->input('price') ? ($request->input('price')) : ($courier->price_type == null ? 0.00 : $courier->price_type),
            'fixed_price' => $request->input('fixed_price') ?? $courier->fixed_price,
            'km_price' => $request->input('km_price') ?? $courier->km_price,
            'online' => $request->input('online') ?? $courier->online,
            'status' => $request->input('status') ?? $courier->status,
        ]);

        return Json::success('Bilgileriniz Başarıyla Güncellenmiştir', new CourierResource($courier));
    }

    public function updatePassword(Request $request)
    {
        $requestData = Validator::make($request->all(), [
            'password' => 'required|confirmed',
        ]);

        if ($requestData->fails()) {
            return response()->json(['errors' => $requestData->errors()->messages(), 'status' => 400], 400);
        }

        $id = auth('courier')->id();

        $courier = Courier::find($id);

        $courier->update([
            'password' => Hash::make($request->input('password')),
        ]);

        return Json::success('Şifreniz Başarıyla Güncellenmiştir', new CourierResource($courier));
    }

    public function updateStatus(Request $request)
    {
        $requestData = Validator::make($request->all(), [
            'status' => 'required|in:active,passive,break',
        ]);

        if ($requestData->fails()) {
            return response()->json(['errors' => $requestData->errors()->messages(), 'status' => 400], 400);
        }

        $id = auth('courier')->id();

        $courier = Courier::find($id);

        $courier->update([
            'status' => $request->input('status'),
        ]);

        return Json::success('Durumunuz Başarıyla Güncellenmiştir', new CourierResource($courier));
    }

    public function destroy()
    {
        try {
            $id = auth('courier')->id();

            $courier = Courier::find($id);

            if ($courier->status == CourierStatus::service) {
                return Json::error('Üzgünüz, şuan aktif bir paket taşıyor görünüyorsunuz, durumunu kontrol ediniz!');
            }

            $isDelete = $courier->delete();

            if ($isDelete) {
                JWTAuth::invalidate(JWTAuth::getToken());

                return Json::success('Hesabınız Silindi ve Oturumunuz Sonlandırıldı');
            } else {
                return Json::error('Hesabınız Silineedi');
            }

        } catch (JWTException $e) {
            return Json::error($e->getMessage());
        }
    }
}
