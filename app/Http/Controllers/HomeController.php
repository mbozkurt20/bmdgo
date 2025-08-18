<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\District;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    public function index()
    {
        if(Auth::guard('restaurant')->check()){
            return redirect()->route('restaurant.index');
        }else{
            return view('restaurant.login');
        }
    }

    public function dealer(){
        $cities = City::all();
        return view('dealer-register', compact('cities'));
    }

    public function getDistricts($cityId)
    {
        $districts = District::where('city_id', $cityId)->get(['id', 'name']);
        return response()->json($districts);
    }

    public function createDealerRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'required|unique:users',
            'password' => 'required|min:5',
            'lat' => 'required',
            'lng' => 'required',
            'city_id' => 'required',
            'district_id' => 'required',
            'address' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('test', $validator->getMessageBag()->first());
        }

        // Validasyon başarılı ise admin tablosuna kaydet
        User::create([
            'is_active' => false,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'latitude' => $request->input('lat'),
            'longitude' => $request->input('lng'),
            'city_id' => $request->input('city_id'),
            'district_id' => $request->input('district_id'),
            'address' => $request->input('address'),
        ]);

        return redirect()->back()->with('message', 'Başvurunuz Başarıyla Alınmıştır');
    }
}
