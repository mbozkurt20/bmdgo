<?php

namespace App\Http\Controllers;

use App\Enums\EntegraStatusEnum;
use App\Helpers\CourierStatus;
use App\Helpers\EntegraStatusHelper;
use App\Helpers\NotificationHelper;
use App\Helpers\OrdersHelper;
use App\Helpers\OrderStatus;
use App\Models\Courier;
use App\Models\CourierOrder;
use App\Models\Order;
use App\Services\EntegraService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EntegraController extends Controller
{
    public function getRejectReasons($orderId)
    {
        $order = Order::where('id', $orderId)->first();

        if (!$order) {
            return response()->json([
                'success' => false, // Genel başarısızlık
                'msg' => 'Sipariş Bulunamadı.'
            ]);
        }

        $res = EntegraService::rejectOrderStatuses($order->pid);

        // Eğer helper'dan success: false geldiyse (İptal edilemez uyarısı gibi)
        if (isset($res->data) && $res->data->success === false) {
            return response()->json([
                'success' => false,
                'msg' => ucfirst($res->data->msg) ?? 'Bu sipariş iptal edilemez.'
            ]);
        }

        // Başarılı durum: Data içindeki array'i gönderiyoruz
        return response()->json([
            'success' => true,
            'data' => $res ?? [] // Array: [{key: 1, value: 'Stok Yok'}, ...]
        ]);
    }

    public function rejectOrder($orderId, Request $request)
    {
        $res = EntegraService::rejectOrder($orderId, $request->all());

        return response()->json(['success' => true, 'res' => $res]);
    }

    public function updateOrderStatus(Request $request)
    {
        $platform = explode('/', $request->path())[1];
        $order = Order::query()->where('tracking_id', $request->tracking_id)->first();

        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sipariş sistemde bulunamadı veya çoktan güncellenmiş.'
            ], 404);
        }

        $status = $request->input('action');

        if ($status == OrderStatus::UNSUPPLIED) {
            $payload = ['reason' => $request->input('entegraReasonId'), 'note' => $request->input('message')];
            $response = EntegraService::rejectOrder($order->pid, $payload);

            if (!$response->data->success){
                return response()->json($response->data->error, 400);
            }

            Log::info('Restoran İptal Etme: ', (array)json_encode($response));

            $order->entegra_next_status = EntegraStatusEnum::UNSUPPLIED;

            if ($response->data->success) {
                $courierOrder = CourierOrder::where('order_id', $order->id)->first();
                if ($courierOrder) {
                    $courier = Courier::find($courierOrder->courier_id);
                    if ($courier) {
                        $courier->status = CourierStatus::active;;
                        $courier->save();

                        Log::info('Kurye durumu güncellendi', ['courier_id' => $courier->id]);

                        if (OrdersHelper::getOrderSystem(3)) {
                            NotificationHelper::add([
                                'title' => 'Restoran Paketi Reddetti',
                                'description' => $order->tracking_id . ' takip numaralı paket ' . $courier->name . '  restoran tarafından iptal edildi.',
                                'url' => route('admin.balance')
                            ]);
                        }
                    }
                }

                $order->courier_id = -1;
                $order->assigned_at = null;
                $order->message =  $request->input('message');
                $order->status = OrderStatus::UNSUPPLIED;
                $order->update();
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Üzgünüz, lütfen yeniden deneyiniz.'
                ], 404);
            }
        }

        // sipariş bekleniyorda ve hazırlanıyor yapılmak istenirse
        if ($status == OrderStatus::PREPARED && $order->entegra_current_status == EntegraStatusEnum::PENDING) {

            $response = EntegraService::updateOrder($order->pid);
            Log::info("Response". json_encode($response));

            if ($response->success) {
                Log::info("Success" . json_encode($response));
                $order->entegra_current_status = $response->status;
                $order->entegra_next_status = $response->orderStatus;
                $order->status = OrderStatus::PREPARED;
                $order->update();
            } else {
                $order->entegra_current_status = $response->status;
                $order->entegra_next_status = $response->orderStatus;
                $order->status = EntegraStatusHelper::getNameByValue($response->orderStatus);
                $order->update();
            }
        }

        if ($status == OrderStatus::DELIVERED && $order->entegra_current_status == EntegraStatusEnum::HANDOVER) {
            $response = EntegraService::updateOrder($order->pid);
            Log::info("Response". json_encode($response));

            if ($response->success) {
                Log::info("Success" . json_encode($response));
                $order->entegra_current_status = $response->status;
                $order->entegra_next_status = $response->orderStatus;
                $order->status = OrderStatus::DELIVERED;
                $order->update();

                $courierOrder = CourierOrder::where('order_id', $order->id)->first();
                if ($courierOrder) {
                    $courier = Courier::find($courierOrder->courier_id);
                    if ($courier) {
                        $courier->status = CourierStatus::active;;
                        $courier->update();
                        Log::info('Kurye durumu güncellendi', ['courier_id' => $courier->id]);
                    }
                }
            } else {
                $order->entegra_current_status = $response->status;
                $order->entegra_next_status = $response->orderStatus;
                $order->status = EntegraStatusHelper::getNameByValue($response->orderStatus);
                $order->update();
            }
        }
    }
}
