<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helpers\CourierStatus;
use App\Helpers\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\City;
use App\Models\Courier;
use App\Models\District;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    public function home()
    {
        $now = Carbon::now();

        $startTime = Carbon::today()->setTime(0, 0);
        $endTime = Carbon::today()->setTime(23, 59);

        $couriers = Courier::where('status', 'active')->where('restaurant_id', 0)->get();
        $tumu = Order::whereDate('created_at', Carbon::today())->whereHas('restaurant', function ($query) {
            return $query;
        })->orderBy('created_at', 'desc')->get();
        $yemeksepeti = Order::where('platform', 'yemeksepeti')->whereHas('restaurant', function ($query) {
            return $query;
        })->whereBetween('created_at', [$startTime, $endTime])->orderBy('created_at', 'desc')->get();
        $getiryemek = Order::where('platform', 'getir')->whereHas('restaurant', function ($query) {
            return $query;
        })->whereBetween('created_at', [$startTime, $endTime])->orderBy('created_at', 'desc')->get();
        $trendyol = Order::where('platform', 'trendyol')->whereHas('restaurant', function ($query) {
            return $query;
        })->whereBetween('created_at', [$startTime, $endTime])->orderBy('created_at', 'desc')->get();
        $telefonsiparis = Order::where('platform', 'telefonsiparis')->whereHas('restaurant', function ($query) {
            return $query;
        })->whereBetween('created_at', [$startTime, $endTime])->orderBy('created_at', 'desc')->get();
        $migros = Order::where('platform', 'migros')->whereHas('restaurant', function ($query) {
            return $query;
        })->whereBetween('created_at', [$startTime, $endTime])->orderBy('created_at', 'desc')->count();

        $totalExpense = Order::whereBetween('created_at', [$startTime, $endTime])->whereHas('restaurant', function ($query) {
            return $query;
        })->sum('amount');
        $formattedExpense = number_format($totalExpense, 2, '.', ',');
        $averageExpense = Order::whereBetween('created_at', [$startTime, $endTime])->whereHas('restaurant', function ($query) {
            return $query;
        })->avg('amount');
        $formattedAverageExpense = number_format($averageExpense, 2, '.', ',');
        $teslimEdilenSiparisler = Order::where('status', 'DELIVERED')->whereHas('restaurant', function ($query) {
            return $query;
        })->whereBetween('created_at', [$startTime, $endTime])->orderBy('created_at', 'desc')->count();

        // Kurye Sayısı - Total number of couriers
        $totalCouriers = Courier::where('admin_id', auth()->id())->count();
        // Boş Kurye - Count of couriers with "Boş" status
        $idleCouriers = Courier::where('status', CourierStatus::active)->count();
        // Molada Kurye - Count of couriers with "Molada" status
        $breakCouriers = Courier::where('status', CourierStatus::break)->count();
        $serviceCouriers = Courier::where('status', CourierStatus::service)->count();

        return view('superadmin.home', compact('totalCouriers', 'serviceCouriers', 'idleCouriers', 'breakCouriers', 'totalExpense', 'formattedExpense', 'averageExpense', 'formattedAverageExpense', 'telefonsiparis', 'tumu', 'yemeksepeti', 'getiryemek', 'trendyol', 'couriers', 'migros', 'teslimEdilenSiparisler'));
    }
    public function getCourier()
    {
        $couriers = Courier::where('restaurant_id', 0)
            ->where('admin_id', auth()->id())
            ->where('status',CourierStatus::active)
            ->get();

        return response()->json($couriers);
    }
    public function dealer()
    {
        $dealers = User::orderBy('is_active','asc')->get();
        return view('superadmin.dealer.index', compact('dealers'));
    }
    public function ajax(Request $request)
    {
        $tumu = Order::whereDate('created_at', Carbon::today())
           ->orderBy('created_at', 'desc')->with(['restaurant','courier'])->get();

        // Siparişleri duruma göre ayır
        $pending = $tumu->where('status', OrderStatus::PENDING);
        $prepared = $tumu->where('status',  OrderStatus::PREPARED);
        $assigned = $tumu->where('status',  OrderStatus::ASSIGNED);
        $handover = $tumu->where('status',  OrderStatus::HANDOVER);
        $delivered = $tumu->where('status',  OrderStatus::DELIVERED);
        $unsupplied = $tumu->where('status',  OrderStatus::UNSUPPLIED);

        return response()->json([
            'pending' => $pending->values()->all(),
            'prepared' => $prepared->values()->all(),
            'assigned' => $assigned->values()->all(),
            'handover' => $handover->values()->all(),
            'delivered' => $delivered->values()->all(),
            'unsupplied' => $unsupplied->values()->all(),
        ]);
    }
    public function profile()
    {
        $auth = Auth::guard('superadmin')->user();
        return view('superadmin.profile', compact('auth'));
    }
    public function profileUpdate(Request $request)
    {
        $auth = Auth::guard('superadmin')->user();

        if ($request->password){
            $auth->password = Hash::make($request->password);
        }

        $auth->name = $request->input('name');
        $auth->email = $request->input('email');
        $auth->update();

        return redirect()->back()->with('message', 'Bilgileriniz Güncellenmiştir.');
    }
    public function deleteDealer($id)
    {
        $user = User::find($id);
        $dleete = $user->delete();
        if ($dleete) {
            echo 'OK';
        } else {
            echo 'ERR';
        }
    }

    public function createDealer()
    {
        $cities = City::all();
        return view('superadmin.dealer.create', compact('cities'));
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
            'is_active' => true,
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

        return redirect()->back()->with('message', 'Yeni Bayi Başarıyla Eklendi!');
    }
    public function statusDealer($id)
    {
        $daler = User::find($id);
        $delete = $daler->update([
            'is_active' => !$daler->is_active
        ]);

        if ($delete){
            echo 'OK';
        }else{
            echo 'ERR';
        }
    }
    public function editDealer($id)
    {
        $dealer = User::find($id);
        $cities = City::all();
        return view('superadmin.dealer.edit', compact('dealer', 'cities'));
    }

    public function updateDealer($id, Request $request)
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
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        // Admin kaydını bul ve güncelle
        $dealer = User::find($id);

        // Eğer admin kaydı yoksa hata döndürülebilir
        if (!$dealer->exists()) {
            return redirect()->back()->with(['test' => 'Bayi Bulunamadı.']);
        }

        // Güncelleme verilerini hazırla
        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'latitude' => $request->input('lat'),
            'longitude' => $request->input('lng'),
            'city_id' => $request->input('city_id'),
            'district_id' => $request->input('district_id'),
            'address' => $request->input('address'),
        ];

        // Şifre değiştirildiyse hashleyip güncelle
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        // Veritabanında güncelleme işlemi
        $dealer->update($updateData);

        return redirect()->route('superadmin.dealer')->with('message', 'Bayi bilgileri başarıyla güncellendi!');
    }

    public function orders()
    {
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
        // Boş Kurye - Count of couriers with "Boş" status
        $idleCouriers = Courier::where('status', CourierStatus::active)->count();
        // Molada Kurye - Count of couriers with "Molada" status
        $breakCouriers = Courier::where('status', CourierStatus::break)->count();

        return view('superadmin.orders.index', compact('totalCouriers', 'idleCouriers', 'breakCouriers', 'totalExpense', 'formattedExpense', 'averageExpense', 'formattedAverageExpense', 'telefonsiparis', 'tumu', 'yemeksepeti', 'getiryemek', 'trendyol', 'couriers', 'migros', 'teslimEdilenSiparisler'));
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
        // Boş Kurye - Count of couriers with "Boş" status
        $idleCouriers = Courier::where('status', CourierStatus::active)->count();
        // Molada Kurye - Count of couriers with "Molada" status
        $breakCouriers = Courier::where('status', CourierStatus::break)->count();

        $totalExpense = Order::whereBetween('created_at', [$startDate, $endDate])->sum('amount');
        $formattedExpense = number_format($totalExpense, 2, '.', ',');
        $averageExpense = Order::whereBetween('created_at', [$startDate, $endDate])->avg('amount');
        $formattedAverageExpense = number_format($averageExpense, 2, '.', ',');

        return view('superadmin.home', compact('totalCouriers', 'idleCouriers', 'breakCouriers', 'totalExpense', 'formattedExpense', 'averageExpense', 'formattedAverageExpense', 'telefonsiparis', 'tumu', 'yemeksepeti', 'getiryemek', 'trendyol', 'couriers', 'migros', 'startDate', 'endDate'));
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
        // Boş Kurye - Count of couriers with "Boş" status
        $idleCouriers = Courier::where('status', CourierStatus::active)->count();
        // Molada Kurye - Count of couriers with "Molada" status
        $breakCouriers = Courier::where('status', CourierStatus::break)->count();

        // Gerekli diğer veriler ve siparişler ile birlikte view döndürülür
        return view('superadmin.home', compact('totalCouriers', 'idleCouriers', 'breakCouriers', 'totalExpense', 'orders', 'formattedExpense', 'averageExpense', 'formattedAverageExpense', 'telefonsiparis', 'tumu', 'yemeksepeti', 'getiryemek', 'trendyol', 'couriers', 'migros'));
    }
}
