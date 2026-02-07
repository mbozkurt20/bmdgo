<?php

namespace App\Http\Controllers\Api\v2\Courier\Orders;


use App\Enums\EntegraStatusEnum;
use App\Helpers\CourierStatus;
use App\Helpers\EntegraHelper;
use App\Helpers\Json;
use App\Helpers\NotificationHelper;
use App\Helpers\OrdersHelper;
use App\Helpers\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Controllers\EntegraController;
use App\Http\Controllers\GpsYemekController;
use App\Http\Resources\OrderResource;
use App\Models\CourierOrder;
use App\Models\Order;
use App\Models\ProgressPaymentRecord;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index()
    {
        $courier = auth('courier')->user();
        $orders = Order::where('courier_id', $courier->id)
            ->whereDate('created_at', Carbon::today())
            ->orderBy('created_at', 'asc')
            ->whereIn('status', [OrderStatus::PREPARED, OrderStatus::ASSIGNED, OrderStatus::HANDOVER])
            ->get();

        return Json::success('Siparişler', OrderResource::collection($orders));
    }
    public function oldOrders(Request $request)
    {
        $startDate = $request->query('startDate')
            ? Carbon::parse($request->query('startDate'))->startOfDay() // Change to startOfDay
            : Carbon::today()->startOfDay();

        $endDate = $request->query('endDate')
            ? Carbon::parse($request->query('endDate'))->endOfDay()
            : Carbon::today()->endOfDay();

        $courier = auth('courier')->user();
        $orders = Order::query()->where('courier_id', $courier->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'asc')
            ->whereNotIn('status', [OrderStatus::ASSIGNED, OrderStatus::HANDOVER])
            ->get();

        return Json::success('Siparişler', OrderResource::collection($orders));
    }

    public function transfer(Request $request, $orderId)
    {
        $courier = auth('courier')->user();
        $order = Order::find($orderId);

        if (!$order) {
            return response()->json(['message' => 'Sipariş Bulunamadı'], 404);
        }

        if ($order->courier_id != $courier->id) {
            return Json::error('Size atanmamış bir siparişi güncelleyemezsiniz', 401);
        }

        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:255',
            'status' => 'required|in:accident,fault,other',
        ]);

        if ($validator->fails()) {
            return Json::error($validator->errors());
        }

       $fi = CourierOrder::query()->where('order_id', $order->id)
           ->where('courier_id',$courier->id)
           ->whereNull('status')
           ->whereNull('reason')
           ->first();

        $fi->update([
           'reason' => $request->reason,
           'status' => $request->status,
        ]);

        $order->update([
            'courier_id' => -1,
            'assigned_at' => null,
            'status' => OrderStatus::PREPARED,
        ]);

        $courier->status = CourierStatus::passive;
        $courier->update();

        return Json::success('Sipariş boşa çıkarıldı, kurye müsait kuryeye atanıcaktır.');
    }

    public function status(Request $request, $orderId)
    {
        //gpsyemek PREPARED - HANDOVER - DELIVERED - REJECTED
        $courier = auth('courier')->user();
        $order = Order::find($orderId);

        if (!$order) {
            return response()->json(['message' => 'Sipariş Bulunamadı'], 404);
        }

        if ($order->courier_id != $courier->id) {
            return Json::error('Size atanmamış bir siparişi güncelleyemezsiniz', 401);
        }
//
        //kurye teslim aldı
        if ($request->input('order_status_id') == 4) {
            try {
                DB::transaction(function () use ($order, $courier) {

                    // 1. API KONTROLLERİ (Sadece Entegra platformları için)
                    $integratedPlatforms = ['getir', 'yemeksepeti', 'trendyol', 'migros'];

                    if (in_array($order->platform, $integratedPlatforms)) {
                        $response = EntegraHelper::updateOrder($order->pid);

                        // API başarısızsa işlemi durdur, veritabanını güncelleme
                        if (!$response || !$response->success) {
                            throw new \Exception("Entegra sipariş atama hatası: " . ($response->message ?? 'Servis yanıt vermedi'));
                        }

                        // API statülerini işle
                        $order->entegra_status = $response->status;
                        $order->entegra_order_status = $response->orderStatus;
                    }

                    // 2. VERİTABANI GÜNCELLEME (Telefon siparişi ise direkt buraya geçer)
                    // Kurye atama işlemi
                    $order->courier_id = $courier->id;
                    $order->status = OrderStatus::ASSIGNED;
                    $order->save();

                    // Opsiyonel: Kurye durumunu da burada değiştirmek istersen ekleyebilirsin
                    // $courier->status = CourierStatus::on_way;
                    // $courier->save();
                });

            } catch (\Exception $e) {
                // Hata durumunda statü değişmez, kullanıcıya bilgi döner
                return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
            }
        }

        //kurye YOLA ÇIKTI
        if ($request->input('order_status_id') == 3) {
            try {
                DB::transaction(function () use ($order, $courier) {

                    // 1. API KONTROLLERİ (Sadece entegrasyonu olanlar için)
                    if ($order->platform === 'gpsyemek') {
                        $gpsResponse = app(GpsYemekController::class)->updateOrder(new Request([
                            'action'      => OrderStatus::HANDOVER,
                            'tracking_id' => $order->tracking_id,
                        ]));

                        // API hata dönerse her şeyi iptal et (Rollback)
                        if (!$gpsResponse) throw new \Exception("GpsYemek API hatası.");
                    }

                    elseif (in_array($order->platform, ['getir', 'yemeksepeti', 'trendyol', 'migros'])) {
                        $response = EntegraHelper::updateOrder($order->pid);

                        // Entegra başarısızsa statü değişmesin
                        if (!$response || !$response->success) {
                            throw new \Exception("Entegra entegrasyonu başarısız.");
                        }

                        // API'den gelen statü bilgilerini doldur
                        $order->entegra_status = $response->status;
                        $order->entegra_order_status = $response->orderStatus;
                    }

                    // 2. VERİTABANI GÜNCELLEME (Telefon siparişi vb. ise direkt buraya düşer)
                    // Eğer yukarıdaki if'lerden biri throw fırlattıysa buraya hiç gelmez.

                    $order->courier_id = $courier->id;
                    $order->status = OrderStatus::HANDOVER;
                    $order->save();

                    $courier->status = CourierStatus::service;
                    $courier->save();
                });

            } catch (\Exception $e) {
                // Hata durumunda hiçbir veritabanı kaydı değişmez.
                return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
            }
        }

        //teslim edidi
        if ($request->input('order_status_id') == 1) {
            try {
                DB::transaction(function () use ($order, $courier) {

                    // 1. API KONTROLLERİ
                    if ($order->platform === 'gpsyemek') {
                        $gpsResponse = app(GpsYemekController::class)->updateOrder(new Request([
                            'action'      => OrderStatus::DELIVERED,
                            'tracking_id' => $order->tracking_id,
                        ]));

                        // API tarafında bir sorun varsa işlemi iptal et
                        if (!$gpsResponse) throw new \Exception("GpsYemek servis hatası.");
                    }

                    elseif (in_array($order->platform, ['getir', 'yemeksepeti', 'trendyol', 'migros'])) {
                        $response = EntegraHelper::updateOrder($order->pid);

                        // Entegra başarısızsa veya beklenen statü değilse işlemi iptal et
                        if (!$response || !$response->success) {
                            throw new \Exception("Entegra güncelleme başarısız.");
                        }

                        $order->entegra_status = $response->status;
                        $order->entegra_order_status = $response->orderStatus;
                    }

                    // 2. VERİTABANI GÜNCELLEMELERİ (Telefon siparişi vb. ise engelsiz buraya gelir)

                    // Siparişi teslim edildi yap
                    $order->status = OrderStatus::DELIVERED;
                    $order->save();

                    // Kuryeyi boşa çıkar (Aktif yap)
                    $courier->status = CourierStatus::active;
                    $courier->save();

                    // 3. BİLDİRİM İŞLEMİ
                    if (OrdersHelper::getOrderSystem(3)) {
                        NotificationHelper::add([
                            'title' => 'Paket Teslim Edildi',
                            'description' => "{$order->tracking_id} takip numaralı paket {$courier->name} kurye tarafından teslim edildi.",
                            'url' => route('admin.balance')
                        ]);
                    }
                });

            } catch (\Exception $e) {
                // API hatası olduğunda buraya düşer ve hiçbir statü değişmez
                return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
            }
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

        return Json::success('Sipariş Durumu Güncellendi', new OrderResource($order));
    }

    public function report(Request $request)
    {
        $courier = auth('courier')->user();

        $startDate = Carbon::parse($request->startDate)->startOfDay();
        $endDate   = Carbon::parse($request->endDate)->endOfDay();

        $orderCount = Order::where('courier_id', $courier->id)
            ->where('status',OrderStatus::DELIVERED)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $courierOrderIds = CourierOrder::where('courier_id', $courier->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->pluck('order_id');

        // Sipariş listesi (admin ekranında tablo için)
        $orders = Order::whereIn('id', $courierOrderIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        $deliveredOrders = $orders->where('status', OrderStatus::DELIVERED);

        $total = 0;

        $info = "";

        if ($courier->price_type == 'package') {
            $pricePerPackage = (float) $courier->price;
            $total += $orderCount * $pricePerPackage;

            // Paket başı ücret bilgilendirmesi
            $info = "Paket başı sabit ücret sistemine göre; {$orderCount} adet teslimat için paket başı " .
                number_format($pricePerPackage, 2) . " TL üzerinden hesaplama yapılmıştır.";

        } else {
            $kmPrice = (float) $courier->km_price;
            $externalKm = (float) $courier->km_distance_later;
            $fixedPrice = (float) $courier->fixed_price;

            $distanceTotal = $deliveredOrders->sum(function($o) use ($kmPrice, $externalKm) {
                $orderKm = (float) $o->distance;
                $payableKm = max(0, $orderKm - $externalKm);
                return $payableKm * $kmPrice;
            });

            $fixedTotal = $fixedPrice * $orderCount;
            $total += ($distanceTotal + $fixedTotal);

            // Mesafe + Sabit ücret bilgilendirmesi
            $info = "Paket başı sabit " . number_format($fixedPrice, 2) . " TL'ye ek olarak; " .
                "her siparişte ilk {$externalKm} km'den sonraki mesafe için km başına " .
                number_format($kmPrice, 2) . " TL eklenerek hesaplama yapılmıştır.";
        }

        return response()->json([
            'order_count' => $orderCount,
            'total_progress_payment' => number_format($total, 2, '.', ''),
            'calculation_info' => $info // Bilgilendirme metni
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
