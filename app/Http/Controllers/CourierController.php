<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use App\Models\Order;
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
