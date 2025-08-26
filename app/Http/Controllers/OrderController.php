<?php

namespace App\Http\Controllers;

use App\Helpers\CourierStatus;
use App\Helpers\GeoLocation;
use App\Helpers\NotificationHelper;
use App\Helpers\OrdersHelper;
use App\Helpers\OrderStatus;
use App\Models\City;
use App\Models\Restaurant;
use App\Models\RestaurantCoupon;
use App\Traits\RequestTrait;
use App\Models\Categorie;
use App\Models\Courier;
use App\Models\CourierOrder;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
    use RequestTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($link)
    {
        if ($link == "tumu") {
            $couriers = Courier::where('status', 'active')->where('restaurant_id', Auth::user()->id)->get();
            $orders = Order::where('status', 0)->where('restaurant_id', Auth::user()->id)->orderBy('id', 'desc')->whereDate('created_at', Carbon::today())->get();
            return view('restaurant.orders.index', compact('orders', 'couriers'));
        } else {
            $orders = Order::where('status', 0)->where('platform', $link)->where('restaurant_id', Auth::user()->id)->whereDate('created_at', Carbon::today())->orderBy('id', 'desc')->get();
            $couriers = Courier::where('status', 'active')->where('restaurant_id', Auth::user()->id)->get();
            if ($orders) {
                return view('restaurant.orders.index', compact('orders', 'couriers'));
            } else {
                $orders = [];
                return view('restaurant.orders.index', compact('orders'));
            }
        }
    }

    public function sendCourier(Request $request, $orderId, $courierId)
    {
        // Check if the courier exists
        $newCourier = Courier::find($courierId);

        if (!$newCourier) {
            Log::error('Courier not found with ID ' . $courierId);
            return response()->json(['error' => 'Courier not found'], 404);
        }

        DB::beginTransaction();

        try {
            Log::info('Processing order ID: ' . $orderId);

            $order = Order::find($orderId);

            if ($order) {
                // Müsait kurye bul
                $courier = Courier::where('status', CourierStatus::active)
                    ->orderBy('last_assigned_at', 'asc')
                    ->where('admin_id', $order->restaurant->admin_id)   // round robin için
                    ->first();

                if ($courier) {
                    // Siparişi kuryeye ata
                    $order->courier_id = $courier->id;
                    $order->status = OrderStatus::HANDOVER;
                    $order->update();

                    // Kuryeyi busy yap ve son atama zamanını güncelle
                    $courier->status = CourierStatus::service;
                    $courier->last_assigned_at = now();
                    $courier->update();

                    // Yeni siparişi kuryeye atama
                    $newOrderCourier = new CourierOrder();
                    $newOrderCourier->courier_id = $courier->id;
                    $newOrderCourier->order_id = $order->id;
                    $newOrderCourier->save();

                    Log::info("Kurye atandı ve durumu Serviste yapıldı. Sipariş ID: " . $order->id . " Kurye ID: " . $courier->id);

                    if (OrdersHelper::getOrderSystem(3)) {
                        NotificationHelper::add([
                            'title' => 'Paket Kuryeye Atandı',
                            'description' => $order->tracking_id . ' takip numaralı paket ' . $courier->name . ' isimli kuryeye atandı.',
                            'url' => route('admin.balance')
                        ]);
                    }

                    Log::info("Sipariş #{$order->id} kurye #{$courier->id} ile eşlendi.");
                } else {
                    Log::info("Sipariş #{$order->id} için müsait kurye yok.");
                }
            } else {
                Log::error('Order ID ' . $orderId . ' not found in orders table');
                throw new \Exception('Order ID ' . $orderId . ' not found in orders table');
            }


            DB::commit();
            Log::info('Transaction committed successfully');
            return response()->json('OK');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Transaction failed: ' . $e->getMessage());
            return response()->json(['error' => 'Transaction failed: ' . $e->getMessage()], 500);
        }
    }

    public function new()
    {
        $couriers = Courier::where('status', 'active')->where('restaurant_id', Auth::user()->id)->get();
        $customers = Customer::where('status', 'active')->where('restaurant_id', Auth::user()->id)->get();
        $categories = Categorie::where('status', 'active')->where('restaurant_id', Auth::user()->id)->get();
        return view('restaurant.orders.new', compact('customers', 'couriers', 'categories'));
    }

    public function addPOS($id)
    {
        $product = Product::find($id);
        $userId = Auth::user()->id;

        $pos = \Cart::session($userId)->getContent();

        if (count($pos) > 0) {
            \Cart::session($userId)->add(array(
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'attributes' => array(),
                'associatedModel' => $product
            ));

            $items = '<div id="posItem_' . $product->id . '"
                class="item select-none"
              style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;
                     background-color: #f1f1f1; border-radius: 10px; padding: 12px; margin-bottom: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">

            <!-- Ürün Görseli -->
            <div style="flex: 0 0 auto; margin-right: 12px;">
                <img src="' . asset($product->image) . '" alt="Ürün Görseli"
                     style="height: 60px; width: 60px; object-fit: cover; border-radius: 6px;">
                <input type="hidden" name="product_id" value="' . $product->id . '">
            </div>

            <!-- Ürün Bilgileri -->
            <div style="flex: 1 1 auto; min-width: 150px;">
                <div style="font-weight: bold; font-size: 14px; color: #333;">' . $product->name . '</div>
                <div style="color: #555; font-size: 13px;">' . number_format($product->price, 2, ',', '.') . ' ₺</div>
            </div>

            <!-- Adet Butonları -->
            <div style="flex: 0 0 auto; display: flex; align-items: center; gap: 6px; margin-top: 8px;">
                <button type="button" onclick="updateMinus(' . $product->id . ')"
                        style="background-color: #dc3545; border: none; color: white; padding: 4px 8px; border-radius: 4px; cursor: pointer;">
                    <i class="fa fa-minus"></i>
                </button>

                <input type="text" name="quantity" id="quantity_' . $product->id . '" value="1" disabled
                       style="width: 40px; height: 30px; text-align: center; font-weight: bold; font-size: 13px; border: 1px solid #ccc; border-radius: 4px; background-color: white;">

                <button type="button" onclick="updatePlus(' . $product->id . ')"
                        style="background-color: #0d2646; border: none; color: white; padding: 4px 8px; border-radius: 4px; cursor: pointer;">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
        </div>';

            if (\Cart::session($userId)->get($id)->quantity > 1) {
                $durum = "var";
            } else {
                $durum = "yok";
            }
        } else {
            \Cart::session($userId)->add(array(
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'attributes' => array(),
                'associatedModel' => $product
            ));

            $durum = "yok";

            $items = '<div id="posItem_' . $product->id . '"
                class="item select-none"
              style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;
                     background-color: #f1f1f1; border-radius: 10px; padding: 12px; margin-bottom: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">

            <!-- Ürün Görseli -->
            <div style="flex: 0 0 auto; margin-right: 12px;">
                <img src="' . asset($product->image) . '" alt="Ürün Görseli"
                     style="height: 60px; width: 60px; object-fit: cover; border-radius: 6px;">
                <input type="hidden" name="product_id" value="' . $product->id . '">
            </div>

            <!-- Ürün Bilgileri -->
            <div style="flex: 1 1 auto; min-width: 150px;">
                <div style="font-weight: bold; font-size: 14px; color: #333;">' . $product->name . '</div>
                <div style="color: #555; font-size: 13px;">' . number_format($product->price, 2, ',', '.') . ' ₺</div>
            </div>

            <!-- Adet Butonları -->
            <div style="flex: 0 0 auto; display: flex; align-items: center; gap: 6px; margin-top: 8px;">
                <button type="button" onclick="updateMinus(' . $product->id . ')"
                        style="background-color: #dc3545; border: none; color: white; padding: 4px 8px; border-radius: 4px; cursor: pointer;">
                    <i class="fa fa-minus"></i>
                </button>

                <input type="text" name="quantity" id="quantity_' . $product->id . '" value="1" disabled
                       style="width: 40px; height: 30px; text-align: center; font-weight: bold; font-size: 13px; border: 1px solid #ccc; border-radius: 4px; background-color: white;">

                <button type="button" onclick="updatePlus(' . $product->id . ')"
                        style="background-color: #0d2646; border: none; color: white; padding: 4px 8px; border-radius: 4px; cursor: pointer;">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
        </div>';
        }

        $posTotalItem = \Cart::session($userId)->getTotalQuantity();
        $posTotal = number_format(\Cart::session($userId)->getTotal(), 2, ',', '.') . " TL";
        $total = \Cart::session($userId)->getTotal();

        return response()->json(['items' => $items, 'posTotalItem' => $posTotalItem, 'posTotal' => $posTotal, 'durum' => $durum, 'total' => $total]);
    }

    public function updatePlusPOS($id)
    {
        $userId = Auth::user()->id;

        \Cart::session($userId)->update($id, array(
            'quantity' => +1,
        ));

        $posTotalItem = \Cart::session($userId)->getTotalQuantity();
        $posTotal = number_format(\Cart::session($userId)->getTotal(), 2, ',', '.') . " TL";
        $total = \Cart::session($userId)->getTotal();

        return response()->json(['posTotalItem' => $posTotalItem, 'posTotal' => $posTotal, 'total' => $total]);
    }

    public function getPosItems()
    {
        $userId = Auth::user()->id;
        $cart = \Cart::session($userId)->getContent();

        $items = '';

        foreach ($cart as $item) {
            $product = $item->associatedModel;
            $quantity = $item->quantity;

            $items .= '<div id="posItem_' . $product->id . '" class="item select-none" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; background-color: #f1f1f1; border-radius: 10px; padding: 12px; margin-bottom: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">
            <div style="flex: 0 0 auto; margin-right: 12px;">
                <img src="' . asset($product->image) . '" alt="Ürün Görseli"
                     style="height: 60px; width: 60px; object-fit: cover; border-radius: 6px;">
                <input type="hidden" name="product_id" value="' . $product->id . '">
            </div>
            <div style="flex: 1 1 auto; min-width: 150px;">
                <div style="font-weight: bold; font-size: 14px; color: #333;">' . $product->name . '</div>
                <div style="color: #555; font-size: 13px;">' . number_format($product->price, 2, ',', '.') . ' ₺</div>
            </div>
            <div style="flex: 0 0 auto; display: flex; align-items: center; gap: 6px; margin-top: 8px;">
                <button type="button" onclick="updateMinus(' . $product->id . ')"
                        style="background-color: #dc3545; border: none; color: white; padding: 4px 8px; border-radius: 4px; cursor: pointer;">
                    <i class="fa fa-minus"></i>
                </button>

                <input type="text" name="quantity" id="quantity_' . $product->id . '" value="' . $quantity . '" disabled
                       style="width: 40px; height: 30px; text-align: center; font-weight: bold; font-size: 13px; border: 1px solid #ccc; border-radius: 4px; background-color: white;">

                <button type="button" onclick="updatePlus(' . $product->id . ')"
                        style="background-color: #0d2646; border: none; color: white; padding: 4px 8px; border-radius: 4px; cursor: pointer;">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
        </div>';
        }

        $posTotalItem = \Cart::session($userId)->getTotalQuantity();
        $posTotal = number_format(\Cart::session($userId)->getTotal(), 2, ',', '.') . " TL";
        $total = \Cart::session($userId)->getTotal();

        return response()->json([
            'items' => $items,
            'posTotalItem' => $posTotalItem,
            'posTotal' => $posTotal,
            'total' => $total,
            ' durum' => 'var',
        ]);
    }

    public function message(Request $request)
    {
        $order = Order::where('restaurant_id', Auth::user()->id)->where('tracking_id', $request->tracking_id)->first();
        $order->message = $request->message;
        $s = $order->save();

        if ($s) {
            return response()->json(['status' => "OK"]);
        } else {
            return response()->json(['status' => "ERR"]);
        }
    }

    public function storeQuick(Request $request)
    {
        try {
            DB::beginTransaction();

            $location = GeoLocation::getLatLong($request->address);

            if (isset($location['error'])) {
                return response()->json(['message' => 'Lütfen Adres Bilginizi Detaylı ve Anlaşılır Yazınız.'], 400);
            }

            $customer = Customer::where('phone', $request->phone)->first();

            if (!$customer) {
                $customer = new Customer();
                $customer->restaurant_id = Auth::user()->id;
                $customer->name = $request->input('full_name');
                $customer->phone = $request->input('phone');
                $customer->mobile = $request->input('mobile');
                $customer->email = $request->input('email') ?? null;
                $customer->save();

                $address = CustomerAddress::where('customer_id', $customer->id)
                    ->where('restaurant_id', Auth::guard('restaurant')->id())
                    ->where('latitude', $location['lat'])
                    ->where('longitude', $location['lon'])
                    ->first();

                if (!$address) {
                    $address = new CustomerAddress();
                    $address->customer_id = $customer->id;
                    $address->restaurant_id = Auth::guard('restaurant')->id();
                    $address->name = 'Hızlı Sipariş';
                    $address->sokak_cadde = ' ';
                    $address->bina_no = ' ';
                    $address->kat = ' ';
                    $address->latitude = $location['lat'];
                    $address->longitude = $location['lon'];
                    $address->daire_no = ' ';
                    $address->mahalle = ' ';
                    $address->adres_tarifi = ' ';
                    $address->save();
                }
            }

            $restaurant = Restaurant::find($request->restaurant_id);
            $order = \App\Models\Order::create([
                'platform' => 'telefonsiparis',
                'courier_id' => $request->courier_id ?? -1,
                'customer_id' => $customer->id,
                'restaurant_id' => $request->restaurant_id,
                'tracking_id' => "POS-" . rand(9, 99999),
                'full_name' => $request->full_name,
                'phone' => $request->phone,
                'address' => $request->address,
                'payment_method' => $request->payment_method,
                'sub_amount' => $request->amount,
                'discount' => 0.00,
                'amount' => $request->amount,
                'status' => OrderStatus::PENDING,
                'items' => json_encode([]),
                'distance' => OrdersHelper::haversineDistance(
                    $restaurant->latitude,
                    $restaurant->longitude,
                    $address->latitude,
                    $address->longitude
                )
            ]);

            if (!$order) {
                DB::rollBack();
                return response()->json(['message' => 'Sipariş Alınamadı!!']);
            }

            DB::commit();

            return response()->json(['message' => 'Sipariş Başarıyla Alındı.']);

        } catch (\Exception $e) {
            DB::rollBack();
            // Hata loglama yapılabilir
            return response()->json(['message' => 'Sipariş İşleminde Hata Oluştu.', 'error' => $e->getMessage()], 500);
        }

    }

    public function message2(Request $request)
    {
        $order = Order::where('restaurant_id', Auth::user()->id)->where('tracking_id', $request->tracking_id)->first();
        $order->message2 = $request->message2;
        $s = $order->save();

        if ($s) {
            return response()->json(['status' => "OK"]);
        } else {
            return response()->json(['status' => "ERR"]);
        }
    }

    public function updateMinusPOS($id, $qty)
    {
        $userId = Auth::user()->id;

        if ($qty <= 1) {
            \Cart::session($userId)->remove($id);
        } else {
            \Cart::session($userId)->update($id, array(
                'quantity' => -1,
            ));
        }

        $posTotalItem = \Cart::session($userId)->getTotalQuantity();
        $posTotal = number_format(\Cart::session($userId)->getTotal(), 2, ',', '.') . " TL";
        $total = \Cart::session($userId)->getTotal();

        return response()->json(['posTotalItem' => $posTotalItem, 'posTotal' => $posTotal, 'total' => $total]);
    }

    public function removePOS()
    {
        $userId = Auth::user()->id;
        \Cart::session($userId)->clear();
        $TotalQuantity = 0;
        $TotalMoney = number_format(0, 2, ',', '.') . " TL";

        return response()->json(['TotalQuantity' => $TotalQuantity, 'TotalMoney' => $TotalMoney]);
    }

    public function customerpos($id)
    {
        // Müşteri bulunuyor mu kontrol et
        $custom = Customer::find($id);

        if (!$custom) {
            return response()->json(['error' => 'Customer not found'], 404); // Müşteri bulunamazsa 404 hatası döndür
        }

        // Adres bulunuyor mu kontrol et
        $adres = CustomerAddress::where('customer_id', $id)->first();

        // Müşteri adresi bulunamazsa alternatif bir mesaj göster
        if (!$adres) {
            $customer = '<p class="text-white mr-2 logo-text">' . $custom->name . '</p> ' . '<span class="ml-2 text-white"> - ' . $custom->phone . '</span>' . ' <br><span>Adres bulunamadı</span>';
        } else {
            // Müşteri ve adres bilgilerini birleştir
            $customer = '<p class="text-white mr-2 logo-text">' . $custom->name . '</p> ' . '<span class=" ml-2 text-white"> - ' . $custom->phone . '</span>' . ' <br><span>' . $adres->mahalle . ' Mah.' . $adres->sokak_cadde . '.No:' . $adres->bina_no . ' Kat:' . $adres->kat . ' Daire:' . $adres->daire_no . '</span>';
        }

        return response()->json(['customer' => $customer]);
    }

    public function customeradd(Request $request)
    {
        $testMode = env('TEST_MODE');

        if ($testMode) {
            if (Customer::count() > env('TEST_MODE_LIMIT')) {
                return redirect()->back()->with('test', 'Test Modu: Üzgünüz, En Fazla ' . env('TEST_MODE_LIMIT') . ' Kayıt Ekleyebilirsiniz');
            }
        }

        $data = $request->validate([
            'name' => 'required',
            'phone' => 'required'
        ]);

        $custom = Customer::where('phone', $data['phone'])->where('restaurant_id', Auth::user()->id)->first();

        if ($custom) {
            $adres = CustomerAddress::where('customer_id', $custom->id)->first();
            $customer = '<p class="logo-text text-white mr-2">' . $custom->name . '</p> ' . '<span class="ml-2 text-white"> - ' . $adres->phone . '</span>' . ' <br><span>' . $adres->mahalle . ' Mah.' . $adres->sokak_cadde . '.No:' . $request->bina_no . ' Kat:' . $request->kat . ' Daire:' . $request->daire_no . '</span>';

            return response()->json(['customer' => $customer, 'customerid' => $custom->id]);
        } else {
            $create = new Customer();
            $create->restaurant_id = Auth::user()->id;
            $create->name = $data['name'];
            $create->phone = $data['phone'];
            $create->mobile = $request->mobile;
            $create->save();

            if ($request->adres_name) {
                $city = City::find($request->sehir);

                $addre = $request->mahalle . ' mah. ' . $request->sokak_cadde . ' sokak. Bina No:' . $request->bina_no . ' Kat:' . $request->kat . ' Daire No:' . $request->daire_no . ' ' . $city->name;
                $location = GeoLocation::getLatLong($addre);

                if (isset($location['error'])) {
                    return response()->json(['message' => 'Konumu doğru girip tekrar deneyiniz.']);
                }

                $adreses = new CustomerAddress();
                $adreses->restaurant_id = Auth::user()->id;
                $adreses->customer_id = $create->id;
                $adreses->name = $request->adres_name;
                $adreses->sokak_cadde = $request->sokak_cadde;
                $adreses->bina_no = $request->bina_no;
                $adreses->kat = $request->kat;
                $adreses->latitude = $location['lat'];
                $adreses->longitude = $location['lon'];
                $adreses->daire_no = $request->daire_no;
                $adreses->mahalle = $request->mahalle;
                $adreses->adres_tarifi = $request->adres_tarifi;
                $adreses->save();
            }

            $customer = '<p class="logo-text mr-2 text-white">' . $data['name'] . '</p> ' . '<span class="ml-2 text-white"> - ' . $data['phone'] . '</span>' . ' <br><span>' . $request->mahalle . ' Mah.' . $request->sokak_cadde . '.No:' . $request->bina_no . ' Kat:' . $request->kat . ' Daire:' . $request->daire_no . '</span>';
            return response()->json(['customer' => $customer, 'customerid' => $create->id,'message' => 'Müşteri Başarıyla Eklendi']);
        }
    }

    public function addOrder(Request $request)
    {
        if (!OrdersHelper::isTopup(null, Auth::user()->id)) {
            return response()->json(['status' => "BalanceError", 'message' => 'Yetersiz Kontör Bakiyesi']);
        }

        $customer = Customer::where('id', $request->customer_id)
            ->where('restaurant_id', Auth::user()->id)
            ->first();

        if (!$customer) {
            return response()->json(['status' => "ERR", 'message' => 'Müşteri bulunamadı']);
        }

        $customer_address = CustomerAddress::where('customer_id', $request->customer_id)->first();
        if (!$customer_address) {
            return response()->json(['status' => "ERR", 'message' => 'Müşteri Adresi Bulunamadı']);
        }

        if (!is_array($request->products) || count($request->products) === 0) {
            return response()->json(['status' => "ERR", 'message' => 'Ürünler listesi boş veya geçersiz.']);
        }

        $discount = 0;;
        $coupon = [];
        if ($request->coupon_id) {
            $coupon = RestaurantCoupon::find($request->coupon_id);

            $coupon = [
                'couponId' => $coupon->coupon_id,
                'description' => $coupon->name,
                'totalSellerAmount' => $coupon->total_seller_amount,
            ];

            $discount = (float)$coupon['totalSellerAmount'];
        }

        $order = new Order();
        $order->restaurant_id = Auth::user()->id;
        $order->customer_id = $request->customer_id;
        $order->full_name = $customer->name;
        $order->coupon = json_encode($coupon);
        $order->address = $customer_address->mahalle . " Mah. " . $customer_address->sokak_cadde . " Cad/Sk. No:" . $customer_address->bina_no . ". Kat:" . $customer_address->kat . ". D:" . $customer_address->daire_no . " / Adres Tarifi:" . $customer_address->adres_tarifi;
        $order->phone = $customer->phone;
        $order->discount = $discount;
        $order->sub_amount = $request->amount;
        $order->amount = $request->amount - $discount;
        $order->platform = "telefonsiparis";
        $order->tracking_id = "POS-" . rand(9, 99999);
        $order->payment_method = $request->payment_method;
        $order->courier_id = $request->courier_id > 0 ? $request->courier_id : -1;

        $restaurant = Restaurant::find($order->restaurant_id);
        $order->distance = OrdersHelper::haversineDistance(
            $restaurant->latitude,
            $restaurant->longitude,
            $customer_address->latitude,
            $customer_address->longitude
        );

        $items = [];
        foreach ($request->products as $productData) {
            $product = Product::find($productData['product_id']);
            if (!$product) {
                return response()->json(['status' => "ERR", 'message' => 'Ürün bulunamadı: ' . $productData['product_id']]);
            }

            $items[] = [
                "price" => $product->price,
                "unitSellingPrice" => $product->price,
                "quantity" => $productData["quantity"],
                "name" => $product->name,
            ];
        }

        $order->items = json_encode($items);
        $order->status = OrderStatus::PENDING;

        try {
            $order->save();
        } catch (\Exception $e) {
            Log::error('Order creation error: ' . $e->getMessage());
            return response()->json(['status' => "ERR", 'message' => 'Sipariş kaydedilirken bir hata oluştu.']);
        }

        $printed = $this->generateInvoice($order);

        //sipariş eklenince ses bildirimi gerçekleştirir.
        OrdersHelper::createOrderNotification($order);

        return response()->json(['status' => "OK", 'printed' => $printed]);
    }


    private function generateInvoice($order)
    {
        $date = now()->format("d.m.Y H:i:s");

        return view('invoice-template', compact('order', 'date'))->render();  // Fatura görünümü burada oluşturulacak
    }

    public function updateOrderStatus(Request $request)
    {
        // İstekten gelen veriler
        $trackingId = $request->input('tracking_id');
        $action = $request->input('action');
        $message = $request->input('message');

        // Siparişi bul
        $order = Order::where('tracking_id', $trackingId)->with(['restaurant', 'courier'])->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        // Siparişin durumunu ve mesajını güncelle
        $order->status = $action;
        $order->message = $message;
        $saveStatus = $order->update();


        // Güncelleme işlemi başarılıysa yanıt ver
        if ($saveStatus) {
            return response()->json(['status' => "OK", 'order' => $order]);
        } else {
            return response()->json(['status' => "ERR"]);
        }
    }

    public function printed($id)
    {
        $order = Order::where('id', $id)->firstOrFail();
        $customer_address = CustomerAddress::where('customer_id', $order->customer_id)->first();
        $items = json_decode($order->items, false);

        $html = view('receipt', [
            'order' => $order,
            'customer_address' => $customer_address,
            'items' => $items,
            'restaurant' => Restaurant::find($order->restaurant_id),
        ])->render(); // render() ile HTML stringi alın

        return response()->json(['printed' => $html]);
    }

    public function deleteOrder($id)
    {
        $order = Order::where('id', $id)->first();

        $order->delete();

        if ($order) {
            return response()->json(['status' => "OK"]);
        } else {
            return response()->json(['status' => "ERR"]);
        }
    }

    public function checkOrders()
    {
        $orderCount = Order::where('status', 'PENDING')->count();
        return response()->json(['orderCount' => $orderCount]);
    }
}
