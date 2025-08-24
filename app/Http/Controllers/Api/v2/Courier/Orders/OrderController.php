<?php

namespace App\Http\Controllers\Api\v2\Courier\Orders;


use App\Helpers\CourierStatus;
use App\Helpers\Json;
use App\Helpers\NotificationHelper;
use App\Helpers\OrdersHelper;
use App\Helpers\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\CourierOrder;
use App\Models\Notification;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $courier = auth('courier')->user();
        $orders = Order::where('courier_id', $courier->id)
            ->whereDate('created_at', Carbon::today())
            ->orderBy('created_at', 'asc')
            ->whereIn('status', [OrderStatus::PENDING, OrderStatus::DELIVERED, OrderStatus::HANDOVER])
            ->get();

        return Json::success('Siparişler', OrderResource::collection($orders));
    }

    public function status(Request $request, $orderId)
    {
        $courier = auth('courier')->user();
        $order = Order::find($orderId);

        if (!$order) {
            return response()->json(['message' => 'Sipariş Bulunamadı'], 404);
        }

        if ($order->courier_id != $courier->id) {
            return Json::error('Size atanmamış bir siparişi güncelleyemezsiniz', 401);
        }

        if ($request->input('order_status_id') == 1) {
            $status = OrderStatus::DELIVERED;

            if (OrdersHelper::getOrderSystem(3)) {
                NotificationHelper::add([
                    'title' => 'Paket Teslim Edildi',
                    'description' => $order->tracking_id . ' takip numaralı paket ' . $courier->name . '  kurye tarafından teslim edildi.',
                    'url' => route('admin.balance')
                ]);
            }
        }

        if ($request->input('order_status_id') == 2) {
            $status = OrderStatus::UNSUPPLIED;
        }

        $order = Order::where('id', $orderId)->first();
        $order->update(["status" => $status]);

        $courier->status = CourierStatus::active;
        $courier->update();

        return Json::success('Sipariş Durumu Güncellendi', new OrderResource($order));
    }

    public function report(Request $request, $id)
    {
        $courier = Courier::findOrFail($id);

        // Tarih aralığı alıyoruz (varsayılan: bugün)
        $startDate = $request->input('start_date', Carbon::today()->toDateString());
        $endDate   = $request->input('end_date', Carbon::today()->toDateString());

        $startDateObj = Carbon::parse($startDate)->startOfDay();
        $endDateObj   = Carbon::parse($endDate)->endOfDay();

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
        $cashOrders   = $deliveredOrders->where('payment_method', 'Kapıda Nakit ile Ödeme');
        $cardOrders   = $deliveredOrders->where('payment_method', 'Kapıda Kredi Kartı ile Ödeme');
        $ticketOrders = $deliveredOrders->where('payment_method', 'Kapıda Ticket ile Ödeme');

        // Kazanç hesaplama
        if ($courier->price_type == 'package') {
            // Paket başı ücretlendirme
            $totalCash       = $cashOrders->count() * $courier->price;
            $totalCreditCard = $cardOrders->count() * $courier->price;
            $totalTicket     = $ticketOrders->count() * $courier->price;
        } else {
            // Km başı ücretlendirme
            $kmPrice = $courier->km_price;

            $totalCash       = $cashOrders->sum(fn($o) => $o->distance * $kmPrice);
            $totalCreditCard = $cardOrders->sum(fn($o) => $o->distance * $kmPrice);
            $totalTicket     = $ticketOrders->sum(fn($o) => $o->distance * $kmPrice);
        }

        $summary = [
            'order_count'    => $deliveredOrders->count(),
            'cash_orders'    => $cashOrders->count(),
            'card_orders'    => $cardOrders->count(),
            'ticket_orders'  => $ticketOrders->count(),
        ];

        $totals = [
            'cash'        => $totalCash,
            'credit_card' => $totalCreditCard,
            'ticket'      => $totalTicket,
            'overall'     => $totalCash + $totalCreditCard + $totalTicket,
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


    public function verifyOrderCode(Request $request, $orderId): JsonResponse
    {
        $courier = auth('courier')->user();
        $order = Order::find($orderId);

        $code = $request->code;

        if (!$order) {
            return response()->json(['message' => 'Sipariş Bulunamadı'], 404);
        }

        if ($order->courier_id != $courier->id) {
            return Json::error('Size atanmamış bir siparişi güncelleyemezsiniz', 401);
        }


        if (Order::where('verify_code', $code)->where('id', $order->id)->exists()) {
            $order->status = OrderStatus::DELIVERED;
            $order->verify_code = null;
            $order->update();

            return Json::success('Kod başarıyla doğrulandı ve sipariş başarıyla teslim edildi.');
        } else {
            return Json::success('Doğrulama Kodu Eşleşmiyor', null, 401);
        }
    }
}
