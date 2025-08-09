<?php


namespace App\Http\Controllers;

use App\Traits\RequestTrait;
use App\Models\Restaurant;
use App\Models\Customer;
use App\Models\CustomerAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class CallerController extends Controller
{

    use RequestTrait;

    public function login(Request $request)
    {
        // Check email
        $restaurant = Restaurant::where('email', $request->email)->first();

        if ($restaurant) {
            // Check password
            if (!Hash::check($request->password, $restaurant->password)) {
                return response([
                    'message' => 'Bad creds'
                ], 401);
            }

            $response = [
                'userId' => $restaurant->id
            ];

            return response($response, 201);
        } else {

            return response('ERROR', 401);
        }
    }
    public function getPhoneNumber($userId, $phone)
    {
        $phonex = substr($phone, -11, 11); //+9 0 552 725 00 23

        $restaurant_check = Restaurant::find($userId);
        if ($restaurant_check) {
            $customer = Customer::where('restaurant_id', $userId)->where('phone', $phonex)->first();

            if ($customer) {
                $adres = CustomerAddress::where('customer_id', $customer->id)->first();

                $data = [
                    "type" => 1,
                    "id" => $customer->id,
                    "name" => $customer->name,
                    "phone" => $phonex,
                    "adres_name" => $adres->name,
                    "sokak_cadde" => $adres->sokak_cadde,
                    "bina_no" => $adres->bina_no,
                    "kat" => $adres->kat,
                    "daire_no" => $adres->daire_no,
                    "mahalle" => $adres->mahalle,
                    "adres_tarifi" => $adres->adres_tarifi,
                ];
            } else {
                $data = [
                    "type" => 2,
                    "phone" => $phonex
                ];
            }

            $channel = 'getPhoneNumber_' . $userId;
            $this->sendSocketIONotification($channel, $data);
        }

        return response(['status' => 'OK']);
    }
}
