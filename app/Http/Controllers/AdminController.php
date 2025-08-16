<?php

namespace App\Http\Controllers;

use App\Helpers\CourierStatus;
use App\Models\AdminSystemFeature;
use App\Models\Courier;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\RestaurantSystemFeature;
use App\Models\SystemFeature;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\Admin;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use function PHPUnit\Framework\exactly;
use function Symfony\Component\VarDumper\Dumper\esc;

class AdminController extends Controller
{
    use AuthenticatesUsers;

    protected function authenticated(Request $request, $user)
    {
        return redirect()->route('admin.index');
    }
    public function logout(Request $request)
    {
        $this->guard()->logout();

        // $request->session()->invalidate();

        // $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResource([], 204)
            : redirect('/');
    }
    protected function loggedOut(Request $request)
    {
        return redirect()->route('admin.login');
    }
    protected function guard()
    {
        return Auth::guard('admin');
    }

    public function profile()
    {
        return view('admin.profile');
    }

    public function notifications()
    {
        Notification::query()->where('admin_id', Auth::guard('admin')->id())->delete();

        return response()->json(['status' => "OK"]);
    }

    public function notificationDelete($id)
    {
        Notification::where('id',$id)->delete();
        return response()->json(['status' => "OK"]);
    }

    public function profileUpdate(Request $request)
    {
        $auth = Auth::guard('admin')->user();

        if ($request->password){
            $auth->password = Hash::make($request->password);
        }

        $auth->latitude = $request->input('latitude');
        $auth->longitude = $request->input('longitude');
        $auth->name = $request->input('name');
        $auth->phone = $request->input('phone');
        $auth->save();

        return redirect()->back()->with('message', 'Bilgileriniz Güncellenmiştir.');
    }
    public function balance()
    {
        return view('admin.balance.index');
    }
    public function home()
    {
        $now = Carbon::now();

        $startTime = Carbon::today()->setTime(0, 0);
        $endTime = Carbon::today()->setTime(23, 59);

        $couriers = Courier::where('status', 'active')->where('admin_id', auth()->id())->where('restaurant_id', 0)->get();
        $tumu = Order::whereDate('created_at', Carbon::today())->whereHas('restaurant', function($query){
            return $query->where('admin_id', auth()->id());
        })->orderBy('created_at', 'desc')->get();
        $yemeksepeti = Order::where('platform', 'yemeksepeti')->whereHas('restaurant', function($query){
            return $query->where('admin_id', auth()->id());
        })->whereBetween('created_at', [$startTime, $endTime])->orderBy('created_at', 'desc')->get();
        $getiryemek = Order::where('platform', 'getir')->whereHas('restaurant', function($query){
            return $query->where('admin_id', auth()->id());
        })->whereBetween('created_at', [$startTime, $endTime])->orderBy('created_at', 'desc')->get();
        $trendyol = Order::where('platform', 'trendyol')->whereHas('restaurant', function($query){
            return $query->where('admin_id', auth()->id());
        })->whereBetween('created_at', [$startTime, $endTime])->orderBy('created_at', 'desc')->get();
        $telefonsiparis = Order::where('platform', 'telefonsiparis')->whereHas('restaurant', function($query){
            return $query->where('admin_id', auth()->id());
        })->whereBetween('created_at', [$startTime, $endTime])->orderBy('created_at', 'desc')->get();
        $migros = Order::where('platform', 'migros')->whereHas('restaurant', function($query){
            return $query->where('admin_id', auth()->id());
        })->whereBetween('created_at', [$startTime, $endTime])->orderBy('created_at', 'desc')->count();

        $totalExpense = Order::whereBetween('created_at', [$startTime, $endTime])->whereHas('restaurant', function($query){
            return $query->where('admin_id', auth()->id());
        })->sum('amount');
        $formattedExpense = number_format($totalExpense, 2, '.', ',');
        $averageExpense = Order::whereBetween('created_at', [$startTime, $endTime])->whereHas('restaurant', function($query){
            return $query->where('admin_id', auth()->id());
        })->avg('amount');
        $formattedAverageExpense = number_format($averageExpense, 2, '.', ',');
        $teslimEdilenSiparisler = Order::where('status', 'DELIVERED')->whereHas('restaurant', function($query){
            return $query->where('admin_id', auth()->id());
        })->whereBetween('created_at', [$startTime, $endTime])->orderBy('created_at', 'desc')->count();

        // Kurye Sayısı - Total number of couriers
        $totalCouriers = Courier::where('admin_id', auth()->id())->count();
        // Boş Kurye - Count of couriers with "Boş" status
        $idleCouriers = Courier::where('status', CourierStatus::active)->where('admin_id', auth()->id())->count();
        // Molada Kurye - Count of couriers with "Molada" status
        $breakCouriers = Courier::where('status', CourierStatus::break)->where('admin_id', auth()->id())->count();
        $serviceCouriers = Courier::where('status', CourierStatus::service)->count();

        return view('admin.home', compact('totalCouriers','serviceCouriers', 'idleCouriers', 'breakCouriers', 'totalExpense', 'formattedExpense', 'averageExpense', 'formattedAverageExpense', 'telefonsiparis', 'tumu', 'yemeksepeti', 'getiryemek', 'trendyol', 'couriers', 'migros', 'teslimEdilenSiparisler'));
    }
    public function features()
    {
        $admin = Admin::find(Auth::user()->id);
        $adminFeatures = AdminSystemFeature::where('admin_id', $admin->id)->get();
        $features = SystemFeature::all();
        return view('admin.features', compact('admin','features','adminFeatures'));
    }

    public function featuresUpdate($id)
    {
        $admin = Auth::guard('admin')->id();

       $systemFeature = AdminSystemFeature::where('admin_id', $admin)->where('system_feature_id',$id)->first();
        if ($systemFeature) {
            $systemFeature->delete();
        }else{
           AdminSystemFeature::create([
                'admin_id' => $admin,
                'system_feature_id' => $id,
           ]);
        }

         echo 'OK';
    }

    public function auto_order($status)
    {
        $auto = Admin::where('id', Auth::guard('admin')->user()->id)->first();
        $auto->auto_orders = $status;
        $auto->update();

        if ($auto->auto_orders ){
            echo "Active";
        }else{
            echo "Passive";
        }
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
        $serviceCouriers = Courier::where('status', CourierStatus::service)->count();

        $totalExpense = Order::whereBetween('created_at', [$startDate, $endDate])->sum('amount');
        $formattedExpense = number_format($totalExpense, 2, '.', ',');
        $averageExpense = Order::whereBetween('created_at', [$startDate, $endDate])->avg('amount');
        $formattedAverageExpense = number_format($averageExpense, 2, '.', ',');

        return view('admin.home', compact('totalCouriers', 'idleCouriers', 'breakCouriers', 'totalExpense', 'formattedExpense', 'averageExpense', 'formattedAverageExpense', 'telefonsiparis', 'tumu', 'yemeksepeti', 'getiryemek', 'trendyol', 'couriers', 'migros','serviceCouriers'));
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
        $serviceCouriers = Courier::where('status', CourierStatus::service)->count();

        // Gerekli diğer veriler ve siparişler ile birlikte view döndürülür
        return view('admin.home', compact('totalCouriers', 'idleCouriers', 'breakCouriers', 'totalExpense', 'orders', 'formattedExpense', 'averageExpense', 'formattedAverageExpense', 'telefonsiparis', 'tumu', 'yemeksepeti', 'getiryemek', 'trendyol', 'couriers', 'migros','serviceCouriers'));
    }

    public function statistics(Request $request)
    {
        $adminId = auth()->id();

        $startDate = $request->input('start_date') ?? Carbon::today()->toDateString();
        $endDate = $request->input('end_date') ?? Carbon::today()->toDateString();

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // Platform listesi
        $platforms = ['yemeksepeti', 'getir', 'trendyol', 'migros', 'adisyo', 'telefonsiparis'];

        // Siparişleri al
        $orders = Order::whereBetween('created_at', [$start, $end])->get();

        // Günlük toplamları hazırlamak için boş dizi
        $orderStats = [];

        // Günleri belirle
        $dateRange = [];
        for ($date = $start; $date->lte($end); $date->addDay()) {
            $dateRange[] = $date->format('Y-m-d');
        }

        // Platformlara göre günlük siparişleri gruplandır
        foreach ($platforms as $platform) {
            $dailyCounts = [];
            foreach ($dateRange as $dateStr) {
                $count = $orders->where('platform', $platform)
                    ->where('created_at', '>=', $dateStr . ' 00:00:00')
                    ->where('created_at', '<=', $dateStr . ' 23:59:59')
                    ->count();
                $dailyCounts[$dateStr] = $count;
            }
            $orderStats[$platform] = $dailyCounts;
        }

        // Toplam sayılar
        $totalOrders = $orders->count();
        $totalRestaurants = Restaurant::where('admin_id', $adminId)->count();
        $totalCouriers = Courier::where('admin_id', $adminId)->count();

        return view('admin.statistics', [
            'orderStats' => $orderStats,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'labels' => $dateRange,
            'platforms' => $platforms,
            'totalOrders' => $totalOrders,
            'totalRestaurants' => $totalRestaurants,
            'totalCouriers' => $totalCouriers,
        ]);
    }
}
