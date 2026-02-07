<?php

namespace App\Http\Controllers;

use App\Helpers\CourierStatus;
use App\Helpers\EntegraHelper;
use App\Helpers\NotificationHelper;
use App\Helpers\OrdersHelper;
use App\Helpers\OrderStatus;
use App\Models\Courier;
use App\Models\CourierOrder;
use App\Models\Order;
use Illuminate\Http\Request;
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
            switch ($status) {
                case OrderStatus::DELIVERED:
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
                case OrderStatus::UNSUPPLIED:
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

    public function getRejectReasons($orderId)
    {
        $res = EntegraHelper::rejectOrderStatuses($orderId);

        return response()->json(['success' => true, 'res' => $res]);
    }

    public function rejectOrder($orderId, Request $request)
    {
       $res = EntegraHelper::rejectOrder($orderId, $request->all());

       return response()->json(['success' => true, 'res' => $res]);
    }

    public function updateOrderStatus(Request $request)
    {
        $platform = explode('/',$request->path())[1];
        $order = Order::query()->where('tracking_id', $request->tracking_id)->first();

        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sipariş sistemde bulunamadı veya çoktan güncellenmiş.'
            ], 404);
        }

        $status = $request->input('action');
        $entegraStatus = $order->entegra_status;

        switch ($status) {
            case OrderStatus::UNSUPPLIED:
                $res =  EntegraHelper::updateOrder($order->pid);

                if ($res->success){
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

                    $order->entegra_status = $entegraStatus;
                    $order->update();
                }
                break;
            case OrderStatus::PREPARED:
                break;
            case OrderStatus::DELIVERED:
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
        }




    }
}
