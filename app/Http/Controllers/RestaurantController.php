<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use App\Models\Order;
use App\Models\Product;
use App\Models\Restaurant;
use App\Models\Customer;
use App\Models\Categorie;
use App\Models\CourierOrder;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;


class RestaurantController extends Controller
{
    use AuthenticatesUsers;
    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        return redirect()->route('restaurant.index');
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResource([], 204)
            : redirect('/');
    }
    protected function loggedOut(Request $request)
    {
        return redirect()->route('restaurant.login');
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('restaurant');
    }

    public function register(Request $request)
    {

        $data = $request->validate([
            'name' => 'required',
            'restaurant_name' => 'required',
            'email' => 'required|unique:restaurants',
            'phone' => 'required',
            'password' => 'required',
        ]);

        $create = new Restaurant();
        $create->restaurant_code = "RES-" . rand(9, 99999);
        $create->restaurant_name = $data['restaurant_name'];
        $create->name = $data['name'];
        $create->email = $data['email'];
        $create->phone = $data['phone'];
        $create->password = Hash::make($data['password']);
        $create->status = 'deactive';
        $create->save();

        return redirect()->route('restaurant.payment');
    }


    public function home()
    {
        $now = Carbon::now();

        $startTime = Carbon::today()->setTime(0, 0);
        $endTime = Carbon::today()->setTime(23, 59);

        //Couriers
        $couriers = Courier::where('status', 'active')->where('restaurant_id', Auth::user()->id)->get();
        $genelCouriers = Courier::where('status', 'active')->where('restaurant_id', 0)->get();
        $ResActiveCouriers = Courier::where('status', 'active')->where('situation', 'Aktif')->where('restaurant_id', Auth::user()->id)->get();
        $AcitegenelCouriers = Courier::where('status', 'active')->where('situation', 'Aktif')->where('restaurant_id', 0)->get();
        $ActiveCouriers = $ResActiveCouriers->merge($AcitegenelCouriers);
        //Resturants
        $restaurant = Restaurant::where('id', Auth::user()->id)->get();
        $tumu = Order::where('status', '!=', 'UNSUPPLIED')->where('status', '!=', 'DELIVERED')->where('restaurant_id', Auth::user()->id)->whereDate('created_at', Carbon::today())->orderBy('created_at', 'desc')->get();

        $yemeksepeti = Order::where('platform', 'yemeksepeti')->where('restaurant_id', Auth::user()->id)->whereBetween('created_at', [$startTime, $endTime])->orderBy('id', 'desc')->get();
        $getiryemek = Order::where('platform', 'getir')->where('restaurant_id', Auth::user()->id)->whereBetween('created_at', [$startTime, $endTime])->orderBy('id', 'desc')->get();
        $trendyol = Order::where('platform', 'trendyol')->where('restaurant_id', Auth::user()->id)->whereBetween('created_at', [$startTime, $endTime])->orderBy('id', 'desc')->get();
        $telefonsiparis = Order::where('platform', 'telefonsiparis')->where('restaurant_id', Auth::user()->id)->whereBetween('created_at', [$startTime, $endTime])->orderBy('id', 'desc')->get();
        $migros = Order::where('platform', 'migros')->where('restaurant_id', Auth::user()->id)->whereBetween('created_at', [$startTime, $endTime])->orderBy('id', 'desc')->count();
        $totalExpense = Order::where('restaurant_id', Auth::user()->id)->whereBetween('created_at', [$startTime, $endTime])->sum('amount');
        $formattedExpense = number_format($totalExpense, 2, '.', ',');
        $averageExpense = Order::where('restaurant_id', Auth::user()->id)->whereBetween('created_at', [$startTime, $endTime])->avg('amount');
        $formattedAverageExpense = number_format($averageExpense, 2, '.', ',');
        $ActiveSiparisler = Order::where('restaurant_id', Auth::user()->id)->whereDate('created_at', Carbon::today())->whereNotIn('id', CourierOrder::pluck('order_id')->toArray())->orderBy('created_at', 'desc')->get();
        //$adisyo = Order::where('platform', 'adisyo')->where('courier_id', -1)->whereBetween('created_at', [$startTime, $endTime])->orderBy('id', 'desc')->count();
        $customers = Customer::where('status', 'active')->where('restaurant_id', Auth::user()->id)->get();
        $categories = Categorie::where('status', 'active')->where('restaurant_id', Auth::user()->id)->get();
        // Kurye Sayısı - Total number of couriers
        $totalCouriers = Courier::count();

        // Boş Kurye - Count of couriers with "Boş" situation
        $idleCouriers = Courier::where('situation', 'Aktif')->count();

        // Molada Kurye - Count of couriers with "Molada" situation
        $breakCouriers = Courier::where('situation', 'Molada')->count();

        return view('restaurant.home', compact('totalCouriers', 'idleCouriers', 'breakCouriers', 'tumu', 'yemeksepeti', 'getiryemek', 'trendyol', 'couriers', 'migros', 'telefonsiparis', 'customers', 'categories', 'genelCouriers', 'restaurant', 'formattedExpense', 'formattedAverageExpense', 'ActiveSiparisler', 'ActiveCouriers', 'AcitegenelCouriers', 'ResActiveCouriers'));
    }
    public function filterByDate(Request $request)
    {
        // Başlangıç ve bitiş tarihlerini al
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        // Siparişleri tarih aralığına göre filtrele
        $ResActiveCouriers = Courier::where('status', 'active')->where('situation', 'Aktif')->where('restaurant_id', Auth::user()->id)->get();
        $AcitegenelCouriers = Courier::where('status', 'active')->where('situation', 'Aktif')->where('restaurant_id', 0)->get();
        $ActiveCouriers = $ResActiveCouriers->merge($AcitegenelCouriers);
        $tumu = Order::where('status', '!=', 'UNSUPPLIED')->where('status', '!=', 'DELIVERED')->where('restaurant_id', Auth::user()->id)->whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'desc')->get();
        $restaurant = Restaurant::where('id', Auth::user()->id)->get();
        $genelCouriers = Courier::where('status', 'active')->where('restaurant_id', 0)->get();
        $yemeksepeti = Order::where('platform', 'yemeksepeti')->where('restaurant_id', Auth::user()->id)->whereBetween('created_at', [$startDate, $endDate])->orderBy('id', 'desc')->get();
        $getiryemek = Order::where('platform', 'getir')->where('restaurant_id', Auth::user()->id)->whereBetween('created_at', [$startDate, $endDate])->orderBy('id', 'desc')->get();
        $trendyol = Order::where('platform', 'trendyol')->where('restaurant_id', Auth::user()->id)->whereBetween('created_at', [$startDate, $endDate])->orderBy('id', 'desc')->get();
        $telefonsiparis = Order::where('platform', 'telefonsiparis')->where('restaurant_id', Auth::user()->id)->whereBetween('created_at', [$startDate, $endDate])->orderBy('id', 'desc')->get();
        $migros = Order::where('platform', 'migros')->where('restaurant_id', Auth::user()->id)->whereBetween('created_at', [$startDate, $endDate])->orderBy('id', 'desc')->count();
        $customers = Customer::where('status', 'active')->where('restaurant_id', Auth::user()->id)->get();
        $categories = Categorie::where('status', 'active')->where('restaurant_id', Auth::user()->id)->get();
        // Diğer veriler (örnek olarak):
        $couriers = Courier::where('status', 'active')->where('restaurant_id', Auth::user()->id)->get();
        $totalExpense = Order::where('restaurant_id', Auth::user()->id)->whereBetween('created_at', [$startDate, $endDate])->sum('amount');
        $formattedExpense = number_format($totalExpense, 2, '.', ',');
        $averageExpense = Order::where('restaurant_id', Auth::user()->id)->whereBetween('created_at', [$startDate, $endDate])->avg('amount');
        $formattedAverageExpense = number_format($averageExpense, 2, '.', ',');
        $ActiveSiparisler = Order::where('restaurant_id', Auth::user()->id)->whereNotIn('status', ['DELIVERED', 'UNSUPPLIED'])->whereDate('created_at', Carbon::today())->whereNotIn('id', CourierOrder::pluck('order_id')->toArray())->orderBy('created_at', 'desc')->get();
        // Kurye Sayısı - Total number of couriers
        $totalCouriers = Courier::count();
        // Boş Kurye - Count of couriers with "Boş" situation
        $idleCouriers = Courier::where('situation', 'Aktif')->count();
        // Molada Kurye - Count of couriers with "Molada" situation
        $breakCouriers = Courier::where('situation', 'Molada')->count();

        return view('restaurant.home', compact('totalCouriers', 'idleCouriers', 'breakCouriers', 'tumu', 'couriers', 'formattedExpense', 'formattedAverageExpense', 'yemeksepeti', 'getiryemek', 'trendyol', 'telefonsiparis', 'migros', 'customers', 'categories', 'restaurant', 'genelCouriers', 'ActiveSiparisler', 'ActiveCouriers', 'ResActiveCouriers', 'AcitegenelCouriers'));
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
        $ResActiveCouriers = Courier::where('status', 'active')->where('situation', 'Aktif')->where('restaurant_id', Auth::user()->id)->get();
        $AcitegenelCouriers = Courier::where('status', 'active')->where('situation', 'Aktif')->where('restaurant_id', 0)->get();
        $ActiveCouriers = $ResActiveCouriers->merge($AcitegenelCouriers);
        $tumu = Order::where('status', '!=', 'UNSUPPLIED')->where('status', '!=', 'DELIVERED')->where('restaurant_id', Auth::user()->id)->whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'desc')->get();
        $restaurant = Restaurant::where('id', Auth::user()->id)->get();
        $genelCouriers = Courier::where('status', 'active')->where('restaurant_id', 0)->get();
        $yemeksepeti = Order::where('platform', 'yemeksepeti')->where('restaurant_id', Auth::user()->id)->whereBetween('created_at', [$startDate, $endDate])->orderBy('id', 'desc')->get();
        $getiryemek = Order::where('platform', 'getir')->where('restaurant_id', Auth::user()->id)->whereBetween('created_at', [$startDate, $endDate])->orderBy('id', 'desc')->get();
        $trendyol = Order::where('platform', 'trendyol')->where('restaurant_id', Auth::user()->id)->whereBetween('created_at', [$startDate, $endDate])->orderBy('id', 'desc')->get();
        $telefonsiparis = Order::where('platform', 'telefonsiparis')->where('restaurant_id', Auth::user()->id)->whereBetween('created_at', [$startDate, $endDate])->orderBy('id', 'desc')->get();
        $migros = Order::where('platform', 'migros')->where('restaurant_id', Auth::user()->id)->whereBetween('created_at', [$startDate, $endDate])->orderBy('id', 'desc')->count();
        $customers = Customer::where('status', 'active')->where('restaurant_id', Auth::user()->id)->get();
        $categories = Categorie::where('status', 'active')->where('restaurant_id', Auth::user()->id)->get();
        $couriers = Courier::where('status', 'active')->where('restaurant_id', Auth::user()->id)->get();
        $totalExpense = Order::where('restaurant_id', Auth::user()->id)->whereBetween('created_at', [$startDate, $endDate])->sum('amount');
        $formattedExpense = number_format($totalExpense, 2, '.', ',');
        $averageExpense = Order::where('restaurant_id', Auth::user()->id)->whereBetween('created_at', [$startDate, $endDate])->avg('amount');
        $formattedAverageExpense = number_format($averageExpense, 2, '.', ',');
        $ActiveSiparisler = Order::where('restaurant_id', Auth::user()->id)->whereNotIn('status', ['DELIVERED', 'UNSUPPLIED'])->whereDate('created_at', Carbon::today())->whereNotIn('id', CourierOrder::pluck('order_id')->toArray())->orderBy('created_at', 'desc')->get();

        // Seçilen tarih aralığındaki siparişleri filtrele
        $orders = Order::where('restaurant_id', Auth::user()->id)->whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'desc')->get();
        // Kurye Sayısı - Total number of couriers
        $totalCouriers = Courier::count();
        // Boş Kurye - Count of couriers with "Boş" situation
        $idleCouriers = Courier::where('situation', 'Aktif')->count();
        // Molada Kurye - Count of couriers with "Molada" situation
        $breakCouriers = Courier::where('situation', 'Molada')->count();
        // Gerekli diğer veriler ve siparişler ile birlikte view döndürülür
        return view('restaurant.home', compact('totalCouriers', 'idleCouriers', 'breakCouriers', 'tumu', 'couriers', 'formattedExpense', 'formattedAverageExpense', 'yemeksepeti', 'getiryemek', 'trendyol', 'telefonsiparis', 'migros', 'customers', 'categories', 'restaurant', 'genelCouriers', 'orders', 'ActiveSiparisler', 'ActiveCouriers', 'ResActiveCouriers', 'AcitegenelCouriers'));
    }
}
