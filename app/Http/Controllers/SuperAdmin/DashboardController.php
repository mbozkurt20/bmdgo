<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Courier;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    public function index(){
        $startTime = Carbon::today()->setTime(0, 0);
        $endTime = Carbon::today()->setTime(23, 59);

        $tumu = Order::whereDate('created_at', Carbon::today())->whereHas('restaurant', function($query){
            return $query->where('admin_id', auth()->id());
        })->orderBy('created_at', 'desc')->get();

        $yemeksepeti = Order::where('platform', 'yemeksepeti')->whereHas('restaurant', function($query){
            return $query->where('admin_id', auth()->id());
        })->whereBetween('created_at', [$startTime, $endTime])->orderBy('id', 'desc')->get();
        $getiryemek = Order::where('platform', 'getir')->whereHas('restaurant', function($query){
            return $query->where('admin_id', auth()->id());
        })->whereBetween('created_at', [$startTime, $endTime])->orderBy('id', 'desc')->get();
        $trendyol = Order::where('platform', 'trendyol')->whereHas('restaurant', function($query){
            return $query->where('admin_id', auth()->id());
        })->whereBetween('created_at', [$startTime, $endTime])->orderBy('id', 'desc')->get();
        $telefonsiparis = Order::where('platform', 'telefonsiparis')->whereHas('restaurant', function($query){
            return $query->where('admin_id', auth()->id());
        })->whereBetween('created_at', [$startTime, $endTime])->orderBy('id', 'desc')->get();
        $migros = Order::where('platform', 'migros')->whereHas('restaurant', function($query){
            return $query->where('admin_id', auth()->id());
        })->whereBetween('created_at', [$startTime, $endTime])->orderBy('id', 'desc')->count();

        return view('superadmin.home',compact('tumu','yemeksepeti','getiryemek','trendyol','telefonsiparis','migros','getiryemek'));
    }

    public function dealer(){
        $dealers = Admin::where('id','!=', 1)->get();
        return view('superadmin.dealer.index', compact('dealers'));
    }

    public function createDealer(){
        return view('superadmin.dealer.create');
    }

    public function createDealerRequest(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
            // Validasyon başarılı ise admin tablosuna kaydet
        DB::table('admins')->insert([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
			'default_locations_lat' => $request->input('lat'),
			'default_locations_lon' => $request->input('lon'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('superadmin.dealer')->with('success', 'Yeni admin başarıyla eklendi!');
    }

    public function editDealer($id){
        $dealer = Admin::find($id);
        return view('superadmin.dealer.edit', compact('dealer'));
    }

    public function updateDealer($id, Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,' . $id,
            'password' => 'nullable|min:8', // Şifre boş olabilir
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        // Admin kaydını bul ve güncelle
        $admin = DB::table('admins')->where('id', $id);

        // Eğer admin kaydı yoksa hata döndürülebilir
        if (!$admin->exists()) {
            return redirect()->back()->withErrors(['error' => 'Admin bulunamadı.']);
        }

        // Güncelleme verilerini hazırla
        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
			'default_locations_lat' => $request->input('lat'),
			'default_locations_lon' => $request->input('lon'),
            'updated_at' => now(),
        ];

        // Şifre değiştirildiyse hashleyip güncelle
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        // Veritabanında güncelleme işlemi
        $admin->update($updateData);

        return redirect()->route('superadmin.dealer')->with('success', 'Admin bilgileri başarıyla güncellendi!');
    }

    public function orders(){
        $now = Carbon::now();

        $startTime = Carbon::today()->setTime(0, 0);
        $endTime = Carbon::today()->setTime(23, 59);

        $couriers = Courier::where('status', 'active')->where('restaurant_id', 0)->get();
        $tumu = Order::whereDate('created_at', Carbon::today())->orderBy('created_at', 'desc')->get();
        $yemeksepeti = Order::where('platform', 'yemeksepeti')->whereBetween('created_at', [$startTime, $endTime])->orderBy('id', 'desc')->get();
        $getiryemek = Order::where('platform', 'getir')->whereBetween('created_at', [$startTime, $endTime])->orderBy('id', 'desc')->get();
        $trendyol = Order::where('platform', 'trendyol')->whereBetween('created_at', [$startTime, $endTime])->orderBy('id', 'desc')->get();
        $telefonsiparis = Order::where('platform', 'telefonsiparis')->whereBetween('created_at', [$startTime, $endTime])->orderBy('id', 'desc')->get();
        $migros = Order::where('platform', 'migros')->whereBetween('created_at', [$startTime, $endTime])->orderBy('id', 'desc')->count();

        $totalExpense = Order::whereBetween('created_at', [$startTime, $endTime])->sum('amount');
        $formattedExpense = number_format($totalExpense, 2, '.', ',');
        $averageExpense = Order::whereBetween('created_at', [$startTime, $endTime])->avg('amount');
        $formattedAverageExpense = number_format($averageExpense, 2, '.', ',');
        $teslimEdilenSiparisler = Order::where('status', 'DELIVERED')->whereBetween('created_at', [$startTime, $endTime])->orderBy('created_at', 'desc')->count();

        // Kurye Sayısı - Total number of couriers
        $totalCouriers = Courier::count();
        // Boş Kurye - Count of couriers with "Boş" situation
        $idleCouriers = Courier::where('situation', 'Aktif')->count();
        // Molada Kurye - Count of couriers with "Molada" situation
        $breakCouriers = Courier::where('situation', 'Molada')->count();

        return view('superadmin.orders.index', compact('totalCouriers', 'idleCouriers', 'breakCouriers', 'totalExpense', 'formattedExpense', 'averageExpense', 'formattedAverageExpense', 'telefonsiparis', 'tumu', 'yemeksepeti', 'getiryemek', 'trendyol', 'couriers', 'migros', 'teslimEdilenSiparisler'));
    }

    public function reports(){

    }
}
