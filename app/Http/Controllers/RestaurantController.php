<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\Customer;
use App\Models\Categorie;
use App\Models\CourierOrder;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class RestaurantController extends Controller
{
    use AuthenticatesUsers;

    protected function authenticated(Request $request, $user)
    {
        return redirect()->route('restaurant.index');
    }

    private function getCommonData($startDate, $endDate)
    {
        $userId = Auth::user()->id;

        $ResActiveCouriers = Courier::where('status', 'active')
            ->where('situation', 'Aktif')
            ->where('restaurant_id', $userId)
            ->get();

        $AcitegenelCouriers = Courier::where('status', 'active')
            ->where('situation', 'Aktif')
            ->where('restaurant_id', 0)
            ->get();

        $ActiveCouriers = $ResActiveCouriers->merge($AcitegenelCouriers);

        return [
            'couriers' => Courier::where('status', 'active')->where('restaurant_id', $userId)->get(),
            'genelCouriers' => Courier::where('status', 'active')->where('restaurant_id', 0)->get(),
            'ResActiveCouriers' => $ResActiveCouriers,
            'AcitegenelCouriers' => $AcitegenelCouriers,
            'ActiveCouriers' => $ActiveCouriers,
            'restaurant' => Restaurant::where('id', $userId)->get(),
            'yemeksepeti' => Order::where('platform', 'yemeksepeti')->where('restaurant_id', $userId)->whereBetween('created_at', [$startDate, $endDate])->orderBy('id', 'desc')->get(),
            'getiryemek' => Order::where('platform', 'getir')->where('restaurant_id', $userId)->whereBetween('created_at', [$startDate, $endDate])->orderBy('id', 'desc')->get(),
            'trendyol' => Order::where('platform', 'trendyol')->where('restaurant_id', $userId)->whereBetween('created_at', [$startDate, $endDate])->orderBy('id', 'desc')->get(),
            'telefonsiparis' => Order::where('platform', 'telefonsiparis')->where('restaurant_id', $userId)->whereBetween('created_at', [$startDate, $endDate])->orderBy('id', 'desc')->get(),
            'migros' => Order::where('platform', 'migros')->where('restaurant_id', $userId)->whereBetween('created_at', [$startDate, $endDate])->count(),
            'customers' => Customer::where('status', 'active')->where('restaurant_id', $userId)->get(),
            'categories' => Categorie::where('status', 'active')->where('restaurant_id', $userId)->get(),
            'formattedExpense' => number_format(Order::where('restaurant_id', $userId)->whereBetween('created_at', [$startDate, $endDate])->sum('amount'), 2, '.', ','),
            'formattedAverageExpense' => number_format(Order::where('restaurant_id', $userId)->whereBetween('created_at', [$startDate, $endDate])->avg('amount'), 2, '.', ','),
            'totalCouriers' => Courier::count(),
            'idleCouriers' => Courier::where('situation', 'Aktif')->count(),
            'breakCouriers' => Courier::where('situation', 'Molada')->count(),
        ];
    }

    public function home()
    {
        $startTime = Carbon::today()->startOfDay();
        $endTime = Carbon::today()->endOfDay();
        $userId = Auth::user()->id;

        $commonData = $this->getCommonData($startTime, $endTime);

        $tumu = Order::where('status', '!=', 'UNSUPPLIED')
            ->where('status', '!=', 'DELIVERED')
            ->where('restaurant_id', $userId)
            ->whereDate('created_at', Carbon::today())
            ->orderBy('created_at', 'desc')
            ->get();

        $ActiveSiparisler = Order::where('restaurant_id', $userId)
            ->whereDate('created_at', Carbon::today())
            ->whereNotIn('id', CourierOrder::pluck('order_id')->toArray())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('restaurant.home', array_merge($commonData, compact('tumu', 'ActiveSiparisler')));
    }

    public function filterByDate(Request $request)
    {
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        $userId = Auth::user()->id;

        $commonData = $this->getCommonData($startDate, $endDate);

        $tumu = Order::where('status', '!=', 'UNSUPPLIED')
            ->where('status', '!=', 'DELIVERED')
            ->where('restaurant_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        $ActiveSiparisler = Order::where('restaurant_id', $userId)
            ->whereNotIn('status', ['DELIVERED', 'UNSUPPLIED'])
            ->whereDate('created_at', Carbon::today())
            ->whereNotIn('id', CourierOrder::pluck('order_id')->toArray())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('restaurant.home', array_merge($commonData, compact('tumu', 'ActiveSiparisler')));
    }

    public function filterOrders(Request $request)
    {
        $dateFilter = $request->input('date');
        switch ($dateFilter) {
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
            case 'today':
            default:
                $startDate = Carbon::today()->startOfDay();
                $endDate = Carbon::today()->endOfDay();
                break;
        }

        $userId = Auth::user()->id;
        $commonData = $this->getCommonData($startDate, $endDate);

        $tumu = Order::where('status', '!=', 'UNSUPPLIED')
            ->where('status', '!=', 'DELIVERED')
            ->where('restaurant_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        $orders = Order::where('restaurant_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        $ActiveSiparisler = Order::where('restaurant_id', $userId)
            ->whereNotIn('status', ['DELIVERED', 'UNSUPPLIED'])
            ->whereDate('created_at', Carbon::today())
            ->whereNotIn('id', CourierOrder::pluck('order_id')->toArray())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('restaurant.home', array_merge($commonData, compact('tumu', 'orders', 'ActiveSiparisler')));
    }

    //auth process
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
}
