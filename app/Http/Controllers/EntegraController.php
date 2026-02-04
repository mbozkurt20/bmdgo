<?php

namespace App\Http\Controllers;

use App\Helpers\CourierStatus;
use App\Helpers\EntegraHelper;
use App\Helpers\NotificationHelper;
use App\Helpers\OrdersHelper;
use App\Helpers\OrderStatus;
use App\Models\City;
use App\Models\Courier;
use App\Models\CourierOrder;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EntegraController extends Controller
{
    public function updateOrder(Request $request)
    {
        $status = $request->input('action');
        $orderCode = $request->input('tracking_id');
        $order = Order::where('tracking_id', $orderCode)->first();

        $response = EntegraHelper::updateOrder($order->pid);

        if ($response->success) {
            // API'ye gönderilecek veriyi belirle
            switch ($status) {
                case 'DELIVERED':
                    // Sipariş teslim edildiğinde kuryenin durumu güncelleniyor
                    $courierOrder = CourierOrder::where('order_id', $order->id)->first();
                    if ($courierOrder) {
                        $courier = Courier::find($courierOrder->courier_id);
                        if ($courier) {
                            // Kuryenin durumunu güncelle
                            $courier->status = CourierStatus::active;;
                            $courier->update();
                            Log::info('Kurye durumu güncellendi', ['courier_id' => $courier->id]);
                        }
                    }
                    break;
                case 'UNSUPPLIED':
                    // Sipariş iptal edildiyse kuryenin durumu güncelleniyor
                    $courierOrder = CourierOrder::where('order_id', $order->id)->first();
                    if ($courierOrder) {
                        $courier = Courier::find($courierOrder->courier_id);
                        if ($courier) {
                            // Kuryenin durumunu güncelle
                            $courier->status = CourierStatus::active;;
                            $courier->save();

                            $order->courier_id = -1;
                            $order->assigned_at = null;
                            $order->status = OrderStatus::PREPARED;
                            $order->update();

                            Log::info('Kurye durumu güncellendi', ['courier_id' => $courier->id]);

                            if (OrdersHelper::getOrderSystem(3)) {
                                NotificationHelper::add([
                                    'title' => 'Kurye Paketi Reddetti',
                                    'description' => $order->tracking_id . ' takip numaralı paket ' . $courier->name . '  kurye tarafından teslim edildi.',
                                    'url' => route('admin.balance')
                                ]);
                            }
                        }
                    }
                    break;
            }

            $order->status = $status;
            $success = $order->update();

            if ($success) {
                return response()->json(['status' => "OK"], 200);
            } else {
                Log::error('Sipariş durumu güncellenemedi', ['order_id' => $order->id]);
                return response()->json(['status' => "ERR"], 400);
            }
        } else {
            return response()->json(['status' => ""], 200);
        }
    }

    public function getRejectReasons($orderId,)
    {
        $res = EntegraHelper::rejectOrderStatuses($orderId);

        return response()->json(['success' => true, 'res' => $res]);
    }

    public function rejectOrder($orderId, Request $request)
    {
       $res = EntegraHelper::rejectOrder($orderId, $request->all());

       return response()->json(['success' => true, 'res' => $res]);
    }
}
