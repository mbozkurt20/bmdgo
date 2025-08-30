<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CourierStatus;
use App\Helpers\NotificationHelper;
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
use App\Services\PushNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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

    public function getCourier()
    {
        if (Auth::guard('restaurant')->check()) {
            $adminId = Auth::guard('restaurant')->user()->admmin_id;
        }

        if (Auth::guard('admin')->check()) {
            $adminId = Auth::guard('admin')->user()->id;
        }

        $couriers = Courier::
        where('admin_id', $adminId)
            ->where('status', CourierStatus::active)
            ->get();

        return response()->json($couriers);
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
                return redirect()->back()->with('test', 'Test Modu: Üzgünüz, En Fazla ' . env('TEST_MODE_LIMIT') . ' Kayıt Ekleyebilirsiniz');
            }
        }

        if (Courier::where('phone', $request->input('phone'))->exists()) {
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

        $courier = Courier::whereId($request->input('id'))->first();

        $courier->update([
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

        $courierss = Courier::where('status', 1)
            ->where('status', CourierStatus::active)
            ->get();


        $admin = Admin::where('id', auth()->id())->select(['latitude', 'longitude'])->first();

        $courierss = $courierss->map(function ($courier) use ($admin) {
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

        Pusher::trigger('courier-channel', 'courier-' . $admin->id, $courierss);

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

    public function report(Request $request, $id)
    {
        $courier = Courier::findOrFail($id);

        // Tarih aralığı alıyoruz (varsayılan: bugün)
        $startDate = $request->input('start_date', Carbon::today()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());

        $startDateObj = Carbon::parse($startDate)->startOfDay();
        $endDateObj = Carbon::parse($endDate)->endOfDay();

        // Kurye'nin eşleşmiş siparişleri
        $courierOrderIds = CourierOrder::where('courier_id', $courier->id)
            ->whereBetween('created_at', [$startDateObj, $endDateObj])
            ->pluck('order_id');

        // Sipariş listesi (admin ekranında tablo için)
        $orders = Order::whereIn('id', $courierOrderIds)
            ->whereBetween('created_at', [$startDateObj, $endDateObj])
            ->orderBy('created_at', 'desc')
            ->get();

        // Teslim edilen siparişler
        $deliveredOrders = $orders->where('status', OrderStatus::DELIVERED);

        // Ödeme yöntemine göre filtreleme
        $cashOrders = $deliveredOrders->where('payment_method', 'Kapıda Nakit ile Ödeme');
        $cardOrders = $deliveredOrders->where('payment_method', 'Kapıda Kredi Kartı ile Ödeme');
        $ticketOrders = $deliveredOrders->where('payment_method', 'Kapıda Ticket ile Ödeme');

        // Kazanç hesaplama
        if ($courier->price_type == 'package') {
            // Paket başı ücretlendirme
            $totalCash = $cashOrders->count() * $courier->price;
            $totalCreditCard = $cardOrders->count() * $courier->price;
            $totalTicket = $ticketOrders->count() * $courier->price;
        } else {
            // Km başı ücretlendirme
            $kmPrice = $courier->km_price;

            $totalCash = $cashOrders->sum(fn($o) => $o->distance * $kmPrice);
            $totalCreditCard = $cardOrders->sum(fn($o) => $o->distance * $kmPrice);
            $totalTicket = $ticketOrders->sum(fn($o) => $o->distance * $kmPrice);
        }

        $summary = [
            'order_count' => $deliveredOrders->count(),
            'cash_orders' => $cashOrders->count(),
            'card_orders' => $cardOrders->count(),
            'ticket_orders' => $ticketOrders->count(),
        ];

        $totals = [
            'cash' => $totalCash,
            'credit_card' => $totalCreditCard,
            'ticket' => $totalTicket,
            'overall' => $totalCash + $totalCreditCard + $totalTicket,
        ];

        return view('admin.couriers.report', compact(
            'courier',
            'orders',
            'startDate',
            'endDate',
            'summary',
            'totals'
        ));
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

        $couriers = Courier::whereIn('status', [CourierStatus::active, CourierStatus::service])
            ->where('restaurant_id', 0)
            ->where('admin_id', auth()->id())
            ->get();

        $admin = Admin::where('id', \auth()->id())->select(['latitude', 'longitude'])->first();
        $courierss = $couriers->map(function ($courier) use ($admin) {
            $distanceKm = OrdersHelper::haversineDistance(
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

        return view('admin.couriers.new-maps', compact('courierss', 'data'));
    }

    public function auto_order($id)
    {
        $auto = Admin::where('id', auth()->id())->first();
        $auto->auto_orders = $id;
        $auto->save();
    }

    /**
     * @throws \Exception
     */
    public function sendCourier($orderId, $courierId)
    {
        $order = Order::find($orderId);
        $courier = Courier::find($courierId);

        $order->courier_id = $courier->id;
        $order->status = OrderStatus::HANDOVER;
        $order->save();

        // Kuryeyi servide de yap ve son atama zamanını güncelle
        $courier->status = CourierStatus::service;
        $courier->last_assigned_at = now();
        $courier->save();

        $orderCourier = CourierOrder::where('courier_id', $courier->id)->where('order_id', $order->id)->first();

        if (!$orderCourier) {
            // Yeni siparişi kuryeye atama
            $newOrderCourier = new CourierOrder();
            $newOrderCourier->courier_id = $courier->id;
            $newOrderCourier->order_id = $order->id;
            $newOrderCourier->save();

            Log::info("Kurye atandı ve durumu Serviste yapıldı. Sipariş ID: " . $order->id . " Kurye ID: " . $courier->id);
        }

        //mobil bildiri
        if ($courier->fcm_token) {
            $ser = new PushNotificationService();
            $ser->sendNotification($courier->fcm_token, 'Yeni Paketiniz Var', $order->tracking_id . ' takip nolu siparişiniz var');
        }

        if (OrdersHelper::getOrderSystem(3)) {
            NotificationHelper::add([
                'title' => 'Paket Kuryeye Atandı',
                'description' => $order->tracking_id . ' takip numaralı paket ' . $courier->name . ' isimli kuryeye atandı.',
                'url' => route('admin.balance')
            ]);
        }

        echo 'OK';
    }

    public function performance(Request $request)
    {
        $courierId = $request->input('courier_id');
        $period = $request->input('period', 'daily'); // daily, weekly, monthly
        $date = Carbon::parse($request->input('date', now()));

        // Tarih aralığına göre filtre
        $startDate = match ($period) {
            'weekly' => $date->copy()->startOfWeek(),
            'monthly' => $date->copy()->startOfMonth(),
            default => $date->copy()->startOfDay(),
        };
        $endDate = match ($period) {
            'weekly' => $date->copy()->endOfWeek(),
            'monthly' => $date->copy()->endOfMonth(),
            default => $date->copy()->endOfDay(),
        };

        $query = DB::table('courier_status_movements')
            ->select('courier_id', 'status', DB::raw('SUM(duration_seconds) as total_duration'))
            ->whereBetween('started_at', [$startDate, $endDate])
            ->groupBy('courier_id', 'status');

        if ($courierId) {
            $query->where('courier_id', $courierId);
        }

        $statusSummary = $query->get();

        // Günlük en çok aktif olan courier
        $topActiveCourier = DB::table('courier_status_movements')
            ->select('courier_id', DB::raw('SUM(duration_seconds) as active_duration'))
            ->where('status', 'active')
            ->whereBetween('started_at', [$startDate, $endDate])
            ->groupBy('courier_id')
            ->orderByDesc('active_duration')
            ->first();

        // Sağdaki liste: tüm courier'lar için statülere göre sıralama
        $topStatusList = DB::table('courier_status_movements')
            ->select('courier_id', 'status', DB::raw('SUM(duration_seconds) as total_duration'))
            ->whereBetween('started_at', [$startDate, $endDate])
            ->groupBy('courier_id', 'status')
            ->orderByDesc('total_duration')
            ->get();

        $couriers = Courier::where('admin_id', auth()->id())->get();

        return view('admin.couriers.performance', compact(
            'statusSummary',
            'topActiveCourier',
            'topStatusList',
            'period',
            'startDate',
            'endDate',
            'courierId',
            'couriers'
        ));
    }
}
