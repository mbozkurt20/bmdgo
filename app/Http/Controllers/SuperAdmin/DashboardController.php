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

        $tumu = Order::whereDate('created_at', Carbon::today())->orderBy('created_at', 'desc')->get();

        $yemeksepeti = Order::where('platform', 'yemeksepeti')
            ->whereBetween('created_at', [$startTime, $endTime])->orderBy('created_at', 'desc')->get();

        $getiryemek = Order::where('platform', 'getir')
            ->whereBetween('created_at', [$startTime, $endTime])->orderBy('created_at', 'desc')->get();

        $trendyol = Order::where('platform', 'trendyol')
            ->whereBetween('created_at', [$startTime, $endTime])->orderBy('created_at', 'desc')->get();

        $telefonsiparis = Order::where('platform', 'telefonsiparis')
            ->whereBetween('created_at', [$startTime, $endTime])->orderBy('created_at', 'desc')->get();

        $migros = Order::where('platform', 'migros')
            ->whereBetween('created_at', [$startTime, $endTime])->orderBy('created_at', 'desc')->count();

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

        $yemeksepeti = Order::where('platform', 'yemeksepeti')
            ->whereBetween('created_at', [$startTime, $endTime])->orderBy('created_at', 'desc')->get();

        $getiryemek = Order::where('platform', 'getir')
            ->whereBetween('created_at', [$startTime, $endTime])->orderBy('created_at', 'desc')->get();

        $trendyol = Order::where('platform', 'trendyol')
            ->whereBetween('created_at', [$startTime, $endTime])->orderBy('created_at', 'desc')->get();

        $telefonsiparis = Order::where('platform', 'telefonsiparis')
            ->whereBetween('created_at', [$startTime, $endTime])->orderBy('created_at', 'desc')->get();

        $migros = Order::where('platform', 'migros')
            ->whereBetween('created_at', [$startTime, $endTime])->orderBy('created_at', 'desc')->count();

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

    public function filterByDate(Request $request)
    {
        // Başlangıç ve bitiş tarihlerini al
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $couriers = Courier::where('status', 'active')->where('restaurant_id', 0)->get();
        $tumu = Order::whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'desc')->get();
        $yemeksepeti = Order::where('platform', 'yemeksepeti')->whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'desc')->get();
        $getiryemek = Order::where('platform', 'getir')->whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'desc')->get();
        $trendyol = Order::where('platform', 'trendyol')->whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'desc')->get();
        $telefonsiparis = Order::where('platform', 'telefonsiparis')->whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'desc')->get();
        $migros = Order::where('platform', 'migros')->whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'desc')->count();
        // Kurye Sayısı - Total number of couriers
        $totalCouriers = Courier::count();
        // Boş Kurye - Count of couriers with "Boş" situation
        $idleCouriers = Courier::where('situation', 'Aktif')->count();
        // Molada Kurye - Count of couriers with "Molada" situation
        $breakCouriers = Courier::where('situation', 'Molada')->count();

        $totalExpense = Order::whereBetween('created_at', [$startDate, $endDate])->sum('amount');
        $formattedExpense = number_format($totalExpense, 2, '.', ',');
        $averageExpense = Order::whereBetween('created_at', [$startDate, $endDate])->avg('amount');
        $formattedAverageExpense = number_format($averageExpense, 2, '.', ',');

        return view('superadmin.home', compact('totalCouriers', 'idleCouriers', 'breakCouriers', 'totalExpense', 'formattedExpense', 'averageExpense', 'formattedAverageExpense', 'telefonsiparis', 'tumu', 'yemeksepeti', 'getiryemek', 'trendyol', 'couriers', 'migros','startDate','endDate'));
    }
    public function filterOrders(Request $request)
    {
        // Tarihe göre aralıkları belirleyelim
        // Tarih filtresini al
        $dateFilter = $request->input('date');
        switch ($dateFilter) {
            case 'today':
                $startDate = Carbon::today()->startOfDay();
                $endDate = Carbon::today()->endOfDay();
                break;
            case 'yesterday':
                $startDate = Carbon::yesterday()->startOfDay();
                $endDate = Carbon::yesterday()->endOfDay();
                break;
            case 'this_week':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                break;
            case 'last_week':
                $startDate = Carbon::now()->subWeek()->startOfWeek();
                $endDate = Carbon::now()->subWeek()->endOfWeek();
                break;
            case 'last_month':
                $startDate = Carbon::now()->subMonth()->startOfMonth();
                $endDate = Carbon::now()->subMonth()->endOfMonth();
                break;
            default:
                // Varsayılan olarak bugünün verilerini döndür
                $startDate = Carbon::today()->startOfDay();
                $endDate = Carbon::today()->endOfDay();
                break;
        }
        $couriers = Courier::where('status', 'active')->where('restaurant_id', 0)->get();
        $tumu = Order::whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'desc')->get();
        $yemeksepeti = Order::where('platform', 'yemeksepeti')->whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'desc')->get();
        $getiryemek = Order::where('platform', 'getir')->whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'desc')->get();
        $trendyol = Order::where('platform', 'trendyol')->whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'desc')->get();
        $telefonsiparis = Order::where('platform', 'telefonsiparis')->whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'desc')->get();
        $migros = Order::where('platform', 'migros')->whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'desc')->count();

        $totalExpense = Order::whereBetween('created_at', [$startDate, $endDate])->sum('amount');
        $formattedExpense = number_format($totalExpense, 2, '.', ',');
        $averageExpense = Order::whereBetween('created_at', [$startDate, $endDate])->avg('amount');
        $formattedAverageExpense = number_format($averageExpense, 2, '.', ',');

        // Seçilen tarih aralığındaki siparişleri filtrele
        $orders = Order::whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'desc')->get();
        // Kurye Sayısı - Total number of couriers
        $totalCouriers = Courier::count();
        // Boş Kurye - Count of couriers with "Boş" situation
        $idleCouriers = Courier::where('situation', 'Aktif')->count();
        // Molada Kurye - Count of couriers with "Molada" situation
        $breakCouriers = Courier::where('situation', 'Molada')->count();

        // Gerekli diğer veriler ve siparişler ile birlikte view döndürülür
        return view('superadmin.home', compact('totalCouriers', 'idleCouriers', 'breakCouriers', 'totalExpense', 'orders', 'formattedExpense', 'averageExpense', 'formattedAverageExpense', 'telefonsiparis', 'tumu', 'yemeksepeti', 'getiryemek', 'trendyol', 'couriers', 'migros'));
    }
}
