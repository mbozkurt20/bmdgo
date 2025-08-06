<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Courier;
use App\Models\Admin;
use App\Models\Order;
use App\Models\CourierOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourierController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $couriers = Courier::where('status', 'active')->where('restaurant_id', 0)->where('admin_id', auth()->id())->get();

        return view('admin.couriers.index', compact('couriers'));
    }

    /**
     * @returns
     */
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
        if (env('TEST_MODE') && Courier::count() == 0){
            $data = $request->validate([
                'name' => 'required',
                'phone' => 'required',
                'password' => 'required',
                'price' => 'required',
            ]);

            $create = new Courier();
            $create->restaurant_id =  0;
            $create->name = $data['name'];
            $create->phone = $data['phone'];
            $create->price = $data['price'];
            $create->password = $data['password'];
            $create->situation = $request->situation??'Aktif';
            $create->admin_id = auth()->id();
            $create->save();

            return redirect()->back()->with('message', 'Kurye Başarıyla Eklendi');
        }else{
            return redirect()->back()->with('test', 'Test Modu: Üzgünüz, En Fazla 1 Kayıt Ekleyebilirsiniz');
        }
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'password' => 'required',
            'price' => 'required',
        ]);

        $create = Courier::find($request->id);
        $create->name = $data['name'];
        $create->phone = $data['phone'];
        $create->price = $data['price'];
        $create->password = $data['password'];
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
        return view('admin.couriers.report', compact('courier', 'orders'));
    }

    public function maps()
    {
        return view('admin.couriers.maps');
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
            $courierx->situation = 'Aktif';
            $courierx->save();

            $ordersor->courier_id = $courier;
            $sav = $ordersor->save();

            $couriery = Courier::where('id', $courier)->first();
            $couriery->situation = 'Serviste';
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
            $courierx->situation = 'Serviste';
            $courierx->save();

            if ($sav) {
                echo "OK";
            } else {
                echo "ERR";
            }
        }
    }
}
