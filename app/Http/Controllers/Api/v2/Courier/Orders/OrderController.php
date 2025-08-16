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
            ->orderBy('created_at', 'desc')
            ->get();

        return Json::success('Siparişler' , OrderResource::collection($orders));
    }

    public function status(Request $request, $orderId)
    {
        $courier = auth('courier')->user();
        $order = Order::find($orderId);

        if (!$order){
            return response()->json(['message' => 'Sipariş Bulunamadı'], 404);
        }

        if ($order->courier_id != $courier->id) {
            return Json::error('Size atanmamış bir siparişi güncelleyemezsiniz',401);
        }

        if ($request->input('order_status_id') == 1){
            $status = OrderStatus::DELIVERED;

            if (OrdersHelper::getOrderSystem(3)){
                NotificationHelper::add([
                    'title' => 'Paket Teslim Edildi',
                    'description' => $order->tracking_id. ' takip numaralı paket '.$courier->name. '  kurye tarafından teslim edildi.',
                    'url' => route('admin.balance')
                ]);
            }
        }

        if ($request->input('order_status_id') == 2){
            $status = OrderStatus::UNSUPPLIED;
        }

        Order::where('id', $orderId)
            ->update(["status" =>  $status]);

        $courier->status = CourierStatus::active;
        $courier->update();

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
        $totalProgressPayment = $courier->price * $orderCount;

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

    public function verifyOrderCode(Request $request,$orderId): JsonResponse
    {
        $courier = auth('courier')->user();
        $order = Order::find($orderId);

        $code = $request->code;

        if (!$order){
            return response()->json(['message' => 'Sipariş Bulunamadı'], 404);
        }

        if ($order->courier_id != $courier->id) {
            return Json::error('Size atanmamış bir siparişi güncelleyemezsiniz',401);
        }


        if (Order::where('verify_code', $code)->where('id',$order->id)->exists()) {
            $order->status = OrderStatus::DELIVERED;
            $order->verify_code = null;
            $order->update();

            return Json::success('Kod başarıyla doğrulandı ve sipariş başarıyla teslim edildi.');
        }else {
            return Json::success('Doğrulama Kodu Eşleşmiyor',null,401);
        }
    }
}
