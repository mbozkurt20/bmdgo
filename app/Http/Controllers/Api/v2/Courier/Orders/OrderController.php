<?php

namespace App\Http\Controllers\Api\v2\Courier\Orders;


use App\Helpers\CourierStatus;
use App\Helpers\Json;
use App\Helpers\NotificationHelper;
use App\Helpers\OrdersHelper;
use App\Helpers\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Courier;
use App\Models\CourierOrder;
use App\Models\Notification;
use App\Models\Order;
use App\Models\ProgressPaymentRecord;
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
            ->whereIn('status', [OrderStatus::ASSIGNED, OrderStatus::HANDOVER])
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

        //teslim edidi
        if ($request->input('order_status_id') == 1) {
            $status = OrderStatus::DELIVERED;

            if (OrdersHelper::getOrderSystem(3)) {
                NotificationHelper::add([
                    'title' => 'Paket Teslim Edildi',
                    'description' => $order->tracking_id . ' takip numaralı paket ' . $courier->name . '  kurye tarafından teslim edildi.',
                    'url' => route('admin.balance')
                ]);
            }

            $order = Order::where('id', $orderId)->first();
            $order->update(["status" => $status]);

            $courier->status = CourierStatus::active;
            $courier->update();
        }

        //reddedildi edidi
        if ($request->input('order_status_id') == 2) {
           $courier->status = CourierStatus::active;
           $courier->update();

           $order->courier_id = -1;
           $order->assigned_at = null;
           $order->status = OrderStatus::PREPARED;
           $order->update();

           $courierOrder = CourierOrder::where('order_id',$order->id)->where('courier_id',$courier->id)->first();
           if ($courierOrder) {
               $courierOrder->delete();
           }

            if (OrdersHelper::getOrderSystem(3)) {
                NotificationHelper::add([
                    'title' => 'Kurye Paketi Reddetti',
                    'description' => $order->tracking_id . ' takip numaralı paket ' . $courier->name . '  kurye tarafından reddedildi..',
                    'url' => route('admin.balance')
                ]);
            }
        }

        //kurye teslim aldı
        if ($request->input('order_status_id') == 3) {
            if ($courier->status == CourierStatus::service){
                return Json::error('Teslim Edilmeyen Sipariş Bulunuyor');
            }

            $courier->status = CourierStatus::service;
            $courier->update();

            $order->courier_id = $courier->id;
            $order->status = OrderStatus::HANDOVER;
            $order->update();
        }

        return Json::success('Sipariş Durumu Güncellendi', new OrderResource($order));
    }

    public function reports(Request $request){
        $courier = auth('courier')->user();

        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');

        if (!$courier || !$startDate || !$endDate) {
            return Json::error('Başlangıç ve bitiş tarihlerini gönderiniz!');
        }

        // Tarih formatlama
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate   = Carbon::parse($endDate)->endOfDay();

        // Kurye'ye ait ilgili tarih aralığındaki sipariş eşlemeleri
        $courierOrderIds = CourierOrder::where('courier_id', $courier->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->pluck('order_id');

        $orderCount = $courierOrderIds->count();
        $totalProgressPayment = ProgressPaymentRecord::where('payable_id',$courier->id)
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->where('payable_type','courier')
            ->sum('amount');

        // Sipariş detayları: Sadece belirtilen tarih aralığında olanlar
        $orders = Order::whereIn('id', $courierOrderIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $totalOrders = $orders->count();
        $totalAmount = $orders->sum('amount');
        $totalCash = $orders->where('payment_method', 'Kapıda Nakit ile Ödeme')->sum('amount');
        $totalCreditCard = $orders->where('payment_method', 'Kapıda Kredi Kartı ile Ödeme')->sum('amount');
        $totalTicket = $orders->where('payment_method', 'Kapıda Ticket ile Ödeme')->sum('amount');

        // Geri dönen veri
        return Json::success('Kurye Raporları', [
            'name' => $courier->name,
            'text' =>
                $courier->price_type == 'fixed'
                    ? 'Sabit kazancınız: '.$courier->fixed_price.'₺. Aşağıda km (1 km '. $courier->km_price .'₺) bazlı kazançlarınız listelenmiştir.'
                    : 'Paket ('.$courier->price.'₺) kazançlarınıza ait veriler aşağıda listelenmiştir.',
            'order_count' => $orderCount,
            'total_progress_payment' => number_format($totalProgressPayment, 2) . ' TL',
            'report' => [
                'total_orders'     => $totalOrders,
                'total_amount'     => number_format($totalAmount, 2) . ' TL',
                'cash_payment'     => number_format($totalCash, 2) . ' TL',
                'credit_card'      => number_format($totalCreditCard, 2) . ' TL',
                'ticket_payment'   => number_format($totalTicket, 2) . ' TL',
            ]
        ]);
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

            $courier->status = CourierStatus::active;
            $courier->update();
            return Json::success('Kod başarıyla doğrulandı ve sipariş başarıyla teslim edildi.');
        } else {
            return Json::success('Doğrulama Kodu Eşleşmiyor', null, 401);
        }
    }
}
