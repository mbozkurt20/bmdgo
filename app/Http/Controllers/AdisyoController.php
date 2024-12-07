<?php

namespace App\Http\Controllers;

use App\Helpers\OrdersHelper;
use App\Jobs\AssignOrderToCourier;
use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\Order;
use App\Models\CourierOrder;
use App\Models\Courier;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Events\NewOrderAdded;

class AdisyoController extends Controller
{
	public function index()
	{
		$restaurants = Restaurant::where('adisyo_api_key', '!=', '')->get();
		foreach ($restaurants as $restaurant) {
			$this->orders($restaurant);
		}
	}

	public function GetOrders()
	{
		Http::get('https://panel.esnafexpress.com.tr/api/getPlatformAdisyo');
		sleep(2);
		return redirect()->back();
	}

	private function orders($restaurant)
	{
		$url = 'https://ext.adisyo.com/api/External/v2/RecentOrders';
        // Adisyo Headers
        $header = array(
            'x-api-key: ' . $restaurant->adisyo_api_key,
            'x-api-secret: ' . $restaurant->adisyo_secret_key,
            'x-api-consumer: ' . $restaurant->adisyo_consumer_adi,
            'Accept: application/json',
            'Content-Type: application/json',
            'user-agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0'
        );

        // cURL Http Request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        // cUrl Http Response
        $result = curl_exec($ch);
        // Json Decode
        $content = json_decode($result);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('JSON çözümleme hatası: ' . json_last_error_msg());
            return;
        }

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            Log::error('cURL hatası: ' . $error_msg);
            return;
        }
        curl_close($ch);

        if (!isset($content->data)) {
            Log::error('Veri bulunamadı veya hata oluştu: ' . $result);
            return;
        }

        foreach ($content->data as $row) {
            Log::info(json_encode($row));

            // Customer Data
            $customer = $row->customer;
            // products Data
            $products = $row->products;
            // Customer Full Name Oluşturma
            $customerFullName = $customer->customerName . ' ' . $customer->customerSurname;
            // Adres oluşturma
            $OrderAddress = $customer->address . ' bölge: ' . $customer->region . ' Notes: ' . $customer->addressDescription;

            // Payment Method
            if ($row->paymentMethodName == "Nakit") {
                $paymentMethod = "Kapıda Nakit ile Ödeme";
            } elseif ($row->paymentMethodName == "Kredi Kartı") {
                $paymentMethod = "Kapıda Kredi Kartı ile Ödeme";
            } else {
                $paymentMethod = 'Online Kredi/Banka Kartı';
            }

            $order = Order::where('tracking_id', strval($row->id))->first();
            if ($order) {
                Log::info('Sipariş zaten mevcut: ' . strval($row->id));
                continue;
            }

            // Items EndCode
            $items = [];
            foreach ($products as $product) {
                $items[] = [
                    'name' => $product->productName,
                    'price' => $product->unitPrice,
                    'count' => intval($product->quantity),
                    'items' => [$product]
                ];
            }
            $itemsJson = json_encode($items);


            //Platform Transitions
            if ($row->externalAppName == 'Adisyo') {
                $platformTransition = 'adisyo';
            } else if ($row->externalAppName == 'Trendyol' || $row->externalAppName == 'Trendyol Yemek') {
                $platformTransition = 'trendyol';
            } else if ($row->externalAppName == 'Getir' || $row->externalAppName == 'Getir Yemek') {
                $platformTransition = 'getir';
            } else if ($row->externalAppName == 'Migros' || $row->externalAppName == 'Migros Yemek') {
                $platformTransition = 'migros';
            } else if ($row->externalAppName == 'Yemeksepeti' || $row->externalAppName == 'YemekSepeti DeliveryHero') {
                $platformTransition = 'yemeksepeti';
            }
            // Sipariş verilerini veritabanına ekleme
            $orderData = [
                'platform'       => $platformTransition, //done
                'status'         => 'PENDING', //done
                'restaurant_id'  => $restaurant->id, //done
                'tracking_id'    => strval($row->id), // done
                'full_name'      => $customerFullName, //done
                'phone'          => $customer->customerPhone, //done
                'amount'         => $row->orderTotal, //done
                'payment_method' => $paymentMethod, //done
                'items'          => $itemsJson, //done
                'address'        => $OrderAddress, //done
                'notes'          => $row->orderNote, //done
            ];

            $order = Order::create($orderData);

            //sipariş eklenince ses bildirimi gerçekleştirir.
            OrdersHelper::createOrderNotification($order);

			AssignOrderToCourier::dispatch($order)->onQueue('high');


        }
	}

	public function updateOrder(Request $request)
	{
		// Debugging: Gelen istek verilerini kontrol edin
		\Log::info('Gelen istek verileri:', ['tracking_id' => $request->input('tracking_id'), 'action' => $request->input('action')]);

		// İstekten gelen veriler
		$trackingId = $request->input('tracking_id');
		$action = $request->input('action');
		$message = $request->input('message');
		// Siparişi bul
		$order = Order::where('tracking_id', $trackingId)->first();
		if (!$order) {
			\Log::error('Sipariş bulunamadı', ['tracking_id' => $trackingId]);
			return response()->json(['message' => 'Sipariş bulunamadı'], 404);
		}

		// Sipariş durumunu güncelle
		$order->status = $action;
		$order->message = $message;
		// Sipariş içindeki ürünlerin kontrolü
		$items = json_decode($order->items, true);
		if (!isset($items[0]['items'][0]['orderId'])) {
			\Log::error('OrderId bulunamadı', ['order' => $order->id]);
			return response()->json(['message' => 'OrderId bulunamadı'], 400);
		}

		$orderId = $items[0]['items'][0]['orderId'];

		// Restoran bilgilerini al
		$restaurant = Restaurant::where('id', $order->restaurant_id)->first();
		if (!$restaurant) {
			\Log::error('Restoran bulunamadı', ['restaurant_id' => $order->restaurant_id]);
			return response()->json(['message' => 'Restoran bulunamadı'], 404);
		}

		// API'ye gönderilecek veriyi belirle
		switch ($action) {
			case 'PENDING':
				$type = 'Prepared';
				$data = [
					'OrderId' => $orderId
				];
				break;
			case 'VERIFY':
				$type = 'VERIFY';
				$data = [
					'OrderId' => $orderId
				];
				break;
			case 'VERIFY_SCHEDULED':
				$type = 'VERIFY_SCHEDULED';
				$data = [
					'OrderId' => $orderId
				];
				break;
			case 'PREPARED':
				$type = 'PREPARED';
				$data = [
					'OrderId' => $orderId
				];
			case 'HANDOVER':
				$type = 'OnDelivery';
				$data = [
					'OrderId' => $orderId,
					'CourierId' => $order->courier_id
				];
				break;
			case 'DELIVERED':
				$type = 'Deliver';
				$data = [
					'OrderId' => $orderId,
					'PaymentType' => 1
				];

				// Sipariş teslim edildiğinde kuryenin durumu güncelleniyor
				$courierOrder = CourierOrder::where('order_id', $order->id)->first();
				if ($courierOrder) {
					$courier = Courier::find($courierOrder->courier_id);
					if ($courier) {
						// Kuryenin durumunu güncelle
						$courier->situation = 'Aktif';
						$courier->save();
						\Log::info('Kurye durumu güncellendi', ['courier_id' => $courier->id]);
					}
				}
				break;
			case 'UNSUPPLIED':
				$type = 'Cancel';
				$data = [
					'OrderId' => $orderId
				];

				// Sipariş iptal edildiyse kuryenin durumu güncelleniyor
				$courierOrder = CourierOrder::where('order_id', $order->id)->first();
				if ($courierOrder) {
					$courier = Courier::find($courierOrder->courier_id);
					if ($courier) {
						// Kuryenin durumunu güncelle
						$courier->situation = 'Aktif';
						$courier->save();
						\Log::info('Kurye durumu güncellendi', ['courier_id' => $courier->id]);
					}
				}
				break;
			default:
				\Log::error('Geçersiz işlem', ['action' => $action]);
				return response()->json(['message' => 'Geçersiz işlem'], 400);
		}

		// API URL
		$url = 'https://ext.adisyo.com/api/External/v2/' . $type;

		// API için başlıklar
		$headers = [
			'x-api-key: ' . $restaurant->adisyo_api_key,
			'x-api-secret: ' . $restaurant->adisyo_secret_key,
			'x-api-consumer: ' . $restaurant->adisyo_consumer_adi,
			'Content-Type: application/json',
			'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1'
		];

		// cURL ayarları
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

		// API isteğini çalıştır
		$result = curl_exec($ch);

		// cURL hatalarını kontrol et
		if (curl_errno($ch)) {
			$error_msg = curl_error($ch);
			\Log::error('cURL hatası:', ['error' => $error_msg]);
			curl_close($ch);
			return response()->json(['error' => $error_msg], 500);
		}

		// cURL bağlantısını kapat
		curl_close($ch);

		// API yanıtını loglama (dönen veriyi loglayın)
		\Log::info('API yanıtı:', ['response' => $result]);

		// API yanıtını çözümle
		$content = json_decode($result, true);

		// API yanıtı boşsa hata döndür
		if (!$content) {
			\Log::error('API yanıtı boş', ['response' => $result]);
			return response()->json(['message' => 'API yanıtı alınamadı'], 500);
		}

		// Sipariş durumu başarıyla kaydedildiyse
		$success = $order->save();

		if ($success) {
			return response()->json(['status' => "OK"], 200);
		} else {
			\Log::error('Sipariş durumu güncellenemedi', ['order_id' => $order->id]);
			return response()->json(['status' => "ERR"], 400);
		}
	}
}
