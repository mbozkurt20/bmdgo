<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use App\Models\Courier;
use App\Models\Expenses;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\TenantModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourierController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $couriers = Courier::where('status', 'active')->where('restaurant_id', Auth::user()->id)->get();

        return view('restaurant.couriers.index', compact('couriers'));
    }
    public function maps()
    {
        $couriers = Courier::on('tenant')
            ->where('status',1)
            ->where('situation','active')
            ->get();



        $restaurant = Restaurant::where('id', auth()->id())->select(['latitude','longitude'])->first();

        $couriers = $couriers->map(function($courier) use ($restaurant) {
            $distanceKm = $this->haversineDistance(
                $restaurant->latitude,
                $restaurant->longitude,
                $courier->latitude,
                $courier->longitude
            );

            if ($distanceKm < 1) {
                $courier->distance = round($distanceKm * 1000) . ' metre';
            } else {
                $courier->distance = round($distanceKm, 2) . ' km';
            }

            return $courier;
        });

        return view('currents.courier.maps', compact('couriers','restaurant'));

    }

    public function new()
    {
        return view('restaurant.couriers.new');
    }
    public function edit($id)
    {
        $courier = Courier::find($id);

        return view('restaurant.couriers.edit', compact('courier'));
    }
    public function create(Request $request)
    {
        $testMode = env('TEST_MODE');

        if ($testMode) {
            if (Courier::count() > env('TEST_MODE_LIMIT')) {
                return redirect()->back()->with('test', 'Test Modu: Üzgünüz, En Fazla '.env('TEST_MODE_LIMIT').' Kayıt Ekleyebilirsiniz');
            }
        }

        $data = $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'price' => 'required',
        ]);

        $create = new Courier();
        $create->restaurant_id =  Auth::user()->id;
        $create->name = $data['name'];
        $create->phone = $data['phone'];
        $create->price = $data['price'];
        $create->situation = 'Aktif';
        $create->admin_id = auth()->id();
        $create->save();

        return redirect()->back()->with('message', 'Kurye kaydı tamamlandı.');
    }
    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'price' => 'required',
        ]);

        $create = Courier::find($request->id);
        $create->name = $data['name'];
        $create->phone = $data['phone'];
        $create->price = $data['price'];
        $create->situation = $request->situation;
        $create->save();

        return redirect()->back()->with('message', 'Kurye kaydı güncellendi.');
    }
    public function delete($id)
    {
        $del = Courier::find($id);
        $del->delete();
        if ($del) {
            echo "OK";
        } else {
            echo "ERR";
        }
    }
    public function report($id)
    {
        $courier = Courier::where('id', $id)->first();
        $orders = Order::where('restaurant_id', Auth::user()->id)->where('courier_id', $id)->whereDate('created_at', Carbon::today())->get();
        return view('restaurant.couriers.report', compact('courier', 'orders'));
    }
}
