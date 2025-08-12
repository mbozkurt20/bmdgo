<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CourierStatus;
use App\Helpers\OrdersHelper;
use App\Helpers\OrderStatus;
use App\Helpers\Pusher;
use App\Http\Controllers\Controller;
use App\Models\Courier;
use App\Models\Admin;
use App\Models\Order;
use App\Models\CourierOrder;
use App\Models\TenantModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CourierController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $couriers = Courier::where('restaurant_id', 0)
            ->where('admin_id', auth()->id())
            ->get();

        return view('admin.couriers.index', compact('couriers'));
    }
    public function new()
    {
        return view('admin.couriers.new');
    }
    public function edit($id)
    {
        $courier = Courier::find($id);
        return view('admin.couriers.edit', compact('courier'));
    }
    public function create(Request $request)
    {
        $testMode = env('TEST_MODE');

        if ($testMode) {
            if (Courier::count() > env('TEST_MODE_LIMIT')) {
                return redirect()->back()->with('test', 'Test Modu: Üzgünüz, En Fazla '.env('TEST_MODE_LIMIT').' Kayıt Ekleyebilirsiniz');
            }
        }

        if (Courier::where('phone',$request->input('phone'))->exists()) {
            return redirect()->back()->with('test', 'Bu numaraya ait kurye bulunmaktadır !!');
        }

        Courier::create([
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'password' => Hash::make($request->input('password')),
            'price_type' => $request->input('price_type'),
            'price' => $request->input('price'),
            'km_price' => $request->input('km_price'),
            'fixed_price' => $request->input('fixed_price'),
            'status' => CourierStatus::active,
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'code' => $this->generateCode(),
            'admin_id' => Auth::guard('admin')->user()->id,
        ]);

        return redirect()->back()->with('message', 'Kurye Başarıyla Kaydedildi.');
    }

    public function generateCode()
    {
        $code = rand(100000, 999999);

        if (Courier::where('code', $code)->exists()) {
            $code = rand(100000, 999999);
        }

        return $code;
    }

    public function update(Request $request)
    {
        $requestData = Validator::make($request->all(), [
            'id' => 'required',
            'name' => 'required',
            'phone' => 'required'
        ]);

        if ($requestData->fails()) {
            return redirect()->back()->with('message', 'Tüm alanları doldurunuz.');
        }

        if (!empty($request->input('password'))) {
            Courier::whereId($request->get('id'))->update([
                'password' => $request->input('password')
            ]);
        }

        Courier::whereId($request->input('id'))->update([
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'price_type' => $request->input('price_type'),
            'price' => $request->input('price'),
            'km_price' => $request->input('km_price'),
            'fixed_price' => $request->input('fixed_price'),
            'status' => $request->input('status'),
            'password' => Hash::make($request->input('password')),
        ]);

        $courierss = Courier::where('status',1)
            ->where('status', CourierStatus::active)
            ->get();


        $admin = Admin::where('id', auth()->id())->select(['latitude','longitude'])->first();

        $courierss = $courierss->map(function($courier) use ($admin) {
            $distanceKm = $this->haversineDistance(
                $admin->latitude,
                $admin->longitude,
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

        Pusher::trigger('courier-channel', 'courier-'.$admin->id, $courierss);

        return redirect()->back()->with('message', 'Kurye güncelleme işlemi başarıyla gerçekleşti.');
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

        $orders = Order::where('courier_id', $id)->whereDate('created_at', Carbon::today())->get();
        return view('admin.couriers.report', compact('courier', 'orders'));
    }

    public function maps()
    {
        $data = [
            'active' => Courier::where('status', CourierStatus::active)
                ->where('restaurant_id', 0)
                ->where('admin_id', auth()->id())
                ->count(),
            'passive' => Courier::where('status', CourierStatus::passive)
                ->where('restaurant_id', 0)
                ->where('admin_id', auth()->id())
                ->count(),
            'service' => Courier::where('status', CourierStatus::service)
                ->where('restaurant_id', 0)
                ->where('admin_id', auth()->id())
                ->count(),
            'break' => Courier::where('status', CourierStatus::break)
                ->where('restaurant_id', 0)
                ->where('admin_id', auth()->id())
                ->count()
        ];

        $couriers = Courier::whereIn('status', [CourierStatus::active,CourierStatus::service])
            ->where('restaurant_id', 0)
            ->where('admin_id', auth()->id())
            ->get();

        $admin = Admin::where('id', \auth()->id())->select(['latitude','longitude'])->first();
        $courierss = $couriers->map(function($courier) use ($admin) {
            $distanceKm =  OrdersHelper::haversineDistance(
                $admin->latitude,
                $admin->longitude,
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

        return view('admin.couriers.new-maps',compact('courierss','data'));
    }

    public function auto_order($id)
    {
        $auto = Admin::where('id', auth()->id())->first();
        $auto->auto_orders = $id;
        $auto->save();
    }

    public function sendCourier($orderid, $courier)
    {
        $ordersor = CourierOrder::where('order_id', $orderid)->first();

        if ($ordersor) {

            $courierx = Courier::where('id', $ordersor->courier_id)->first();
            $courierx->status =  CourierStatus::active;;
            $courierx->save();

            $ordersor->courier_id = $courier;
            $sav = $ordersor->save();

            $couriery = Courier::where('id', $courier)->first();
            $couriery->status = CourierStatus::service;
            $couriery->save();

            if ($sav) {
                echo "OK";
            } else {
                echo "ERR";
            }
        } else {

            $order = new CourierOrder();
            $order->courier_id = $courier;
            $order->order_id = $orderid;
            $sav = $order->save();

            $courierx = Courier::where('id', $courier)->first();
            $courierx->status = CourierStatus::service;
            $courierx->save();

            if ($sav) {
                echo "OK";
            } else {
                echo "ERR";
            }
        }
    }
}
