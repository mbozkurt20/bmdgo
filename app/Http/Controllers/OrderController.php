<?php

namespace App\Http\Controllers;

use App\Traits\RequestTrait;
use App\Models\Admin;
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
use App\Jobs\AssignOrderToCourier;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    use RequestTrait;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
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

    // public function sendCourier(Request $request)
    // {

    //     $orders = $request->input('orders'); // Gönderilen sipariş IDs array
    //     $courierId = $request->input('courier_id'); // Seçilen kurye ID
    //     foreach ($orders as $orderId) {

    //         $ordersor = CourierOrder::where('order_id', $orderId)->first();

    //         if ($ordersor) {
    //             $courierx = Courier::where('id', $ordersor->courier_id)->first();
    //             $courierx->situation = 'Aktif';
    //             $courierx->save();

    //             $ordersor->courier_id = $courierId;
    //             $sav = $ordersor->save();

    //             $couriery = Courier::where('id', $courierId)->first();
    //             $couriery->situation = 'Serviste';
    //             $couriery->save();
    //         } else {
    //             $order = new CourierOrder();
    //             $order->courier_id = $courierId;
    //             $order->order_id = $orderId;
    //             $sav = $order->save();

    //             $courierx = Courier::where('id', $courierId)->first();
    //             $courierx->situation = 'Serviste';
    //             $courierx->save();
    //         }
    //     }
    //     return response()->json('OK');
    // }

    public function sendCourier(Request $request)
    {
        $orders = $request->input('orders');
        $courierId = $request->input('courier_id');

        \Log::info('Courier ID:', [$courierId]);
        \Log::info('Orders:', $orders);

        // Check if the courier exists
        $newCourier = Courier::find($courierId);

        if (!$newCourier) {
            \Log::error('Courier not found with ID ' . $courierId);
            return response()->json(['error' => 'Courier not found'], 404);
        }

        DB::beginTransaction();

        try {
            foreach ($orders as $orderId) {
                \Log::info('Processing order ID: ' . $orderId);

                $order = Order::find($orderId);

                if ($order) {
                    \Log::info('Order found: ' . $orderId);

                    $order->courier_id = $courierId;
                    $order->save();
                    \Log::info('Order ID ' . $orderId . ' updated in orders table with courier ID ' . $courierId);

                    $existingCourierOrder = CourierOrder::where('order_id', $orderId)->first();

                    if ($existingCourierOrder) {
                        // Var ise güncelliyoruz
                        $existingCourierOrder->courier_id = $courierId;
                        $existingCourierOrder->save();
                        \Log::info('Order ID ' . $orderId . ' updated in CourierOrder table with courier ID ' . $courierId);
                    } else {
                        $newCourierOrder = new CourierOrder();
                        $newCourierOrder->courier_id = $courierId;
                        $newCourierOrder->order_id = $orderId;

                        if ($newCourierOrder->save()) {
                            \Log::info('Order ID ' . $orderId . ' created in CourierOrder table with courier ID ' . $courierId);
                        } else {
                            \Log::error('Failed to create CourierOrder for order ID: ' . $orderId);
                        }
                    }

                    $newCourier->situation = 'Serviste';
                    $newCourier->save();
                } else {
                    \Log::error('Order ID ' . $orderId . ' not found in orders table');
                    throw new \Exception('Order ID ' . $orderId . ' not found in orders table');
                }
            }

            DB::commit();
            \Log::info('Transaction committed successfully');
            return response()->json('OK');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Transaction failed: ' . $e->getMessage());
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

            $items = '<div id="posItem_' . $product->id . '" class="item select-none mb-3 bg-blue-gray-50 rounded-lg w-full text-blue-gray-700 py-2 px-2 flex justify-center" style="background: #e7e7e7;border-radius: 10px">
                    <div class="row">
                        <div class="col-md-2" >
                            <img src="' . asset($product->image) . '" style="height: 45px;width:65px;border-radius: 10px">
                        </div>
                        <div class="col-md-6 text-left" style="width: 100%;">
                            <span style="width: 100%;">  ' . $product->name . '</span><br>
                            <span style="width: 100%;">' . number_format($product->price, 2, ',', '.') . ' ₺</span>
                        </div>
                        <div class="col-md-4 text-right">
                            <div class="m-btn-group m-btn-group--pill btn-group mr-2" role="group" aria-label="..." style="padding: 3px">
                            <input type="hidden" name="product_id" value="' . $product->id . '">
                                <button type="button" onclick="updateMinus(' . $product->id . ')" class="m-btn btn btn-default" style="background: #5c5c5c;color:#fff"><i class="fa fa-minus"></i></button>
                                <input type="button" class="m-btn btn btn-default" name="quantity" id="quantity_' . $product->id . '" value="1" style="background: #fff;color:#000;font-weight: bold" disabled>
                                <button type="button"  onclick="updatePlus(' . $product->id . ')" class="m-btn btn btn-default" style="background: #5c5c5c;color:#fff"><i class="fa fa-plus"></i></button>
                            </div>
                        </div>
                    </div>
                </div> ';

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

            $items = '<div id="posItem_' . $product->id . '" class="item select-none mb-3 bg-blue-gray-50 rounded-lg w-full text-blue-gray-700 py-2 px-2 flex justify-center" style="background: #e7e7e7;border-radius: 10px">
                    <div class="row">
                        <div class="col-md-2" >
                            <img src="' . asset($product->image) . '" style="height: 45px;width:65px;border-radius: 10px">
                        </div>
                        <div class="col-md-6 text-left" style="width: 100%;">
                            <span style="width: 100%;">  ' . $product->name . '</span><br>
                            <span style="width: 100%;">' . number_format($product->price, 2, ',', '.') . ' ₺</span>
                        </div>
                        <div class="col-md-4 text-right">
                            <div class="m-btn-group m-btn-group--pill btn-group mr-2" role="group" aria-label="..." style="padding: 3px">
                               <input type="hidden" name="product_id" value="' . $product->id . '">
                                <button type="button" onclick="updateMinus(' . $product->id . ')" class="m-btn btn btn-default" style="background: #5c5c5c;color:#fff"><i class="fa fa-minus"></i></button>
                                <input type="button" class="m-btn btn btn-default" name="quantity" id="quantity_' . $product->id . '" value="1" style="background: #fff;color:#000;font-weight: bold" disabled>
                                <button type="button"  onclick="updatePlus(' . $product->id . ')" class="m-btn btn btn-default" style="background: #5c5c5c;color:#fff"><i class="fa fa-plus"></i></button>
                            </div>
                        </div>
                    </div>
                </div> ';
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
            $customer = '<h2 class="logo-text">' . $custom->name . '</h2> ' . $custom->phone . ' <br><span>Adres bulunamadı</span>';
        } else {
            // Müşteri ve adres bilgilerini birleştir
            $customer = '<h2 class="logo-text">' . $custom->name . '</h2> ' . $custom->phone . ' <br><span>' . $adres->mahalle . ' Mah.' . $adres->sokak_cadde . '.No:' . $adres->bina_no . ' Kat:' . $adres->kat . ' Daire:' . $adres->daire_no . '</span>';
        }

        return response()->json(['customer' => $customer]);
    }

    public function customeradd(Request $request)
    {


        $data = $request->validate([
            'name' => 'required',
            'phone' => 'required'
        ]);

        $custom  = Customer::where('phone', $data['phone'])->whereDate('restaurant_id', Auth::user()->id)->first();

        if ($custom) {
            $adres = CustomerAddress::where('customer_id', $custom->id)->first();
            $customer = '<h2 class="logo-text">' . $custom->name . '</h2> ' . $adres->phone . ' <br><span>' . $adres->mahalle . ' Mah.' . $adres->sokak_cadde . '.No:' . $request->bina_no . ' Kat:' . $request->kat . ' Daire:' . $request->daire_no . '</span>';

            return response()->json(['customer' => $customer, 'customerid' => $custom->id]);
        } else {
            $create = new Customer();
            $create->restaurant_id =  Auth::user()->id;
            $create->name = $data['name'];
            $create->phone = $data['phone'];
            $create->mobile = $request->mobile;
            $create->save();

            if ($request->adres_name) {
                $adreses = new CustomerAddress();
                $adreses->restaurant_id =  Auth::user()->id;
                $adreses->customer_id = $create->id;
                $adreses->name = $request->adres_name;
                $adreses->sokak_cadde = $request->sokak_cadde;
                $adreses->bina_no = $request->bina_no;
                $adreses->kat = $request->kat;
                $adreses->daire_no = $request->daire_no;
                $adreses->mahalle = $request->mahalle;
                $adreses->adres_tarifi = $request->adres_tarifi;
                $adreses->save();
            }

            $customer = '<h2 class="logo-text">' . $data['name'] . '</h2> ' . $data['phone'] . ' <br><span>' . $request->mahalle . ' Mah.' . $request->sokak_cadde . '.No:' . $request->bina_no . ' Kat:' . $request->kat . ' Daire:' . $request->daire_no . '</span>';

            return response()->json(['customer' => $customer, 'customerid' => $create->id]);
        }
    }
    public function addOrder(Request $request)
    {
        $customer = Customer::where('id', $request->customer_id)
            ->where('restaurant_id', Auth::user()->id)
            ->first();

        if (!$customer) {
            return response()->json(['status' => "ERR", 'message' => 'Müşteri bulunamadı']);
        }

        $customer_address = CustomerAddress::where('customer_id', $request->customer_id)->first();
        if (!$customer_address) {
            return response()->json(['status' => "ERR", 'message' => 'Müşteri adresi bulunamadı']);
        }

        if (!is_array($request->products) || count($request->products) === 0) {
            return response()->json(['status' => "ERR", 'message' => 'Ürünler listesi boş veya geçersiz.']);
        }

        $order = new Order();
        $order->restaurant_id = Auth::user()->id;
        $order->full_name = $customer->name;
        $order->address = $customer_address->mahalle . " Mah. " . $customer_address->sokak_cadde . " Cad/Sk. No:" . $customer_address->bina_no . ". Kat:" . $customer_address->kat . ". D:" . $customer_address->daire_no . " / Adres Tarifi:" . $customer_address->adres_tarifi;
        $order->phone = $customer->phone;
        $order->amount = $request->amount;
        $order->platform = "telefonsiparis";
        $order->tracking_id = "POS-" . rand(9, 99999);
        $order->payment_method = $request->payment_method;
        $order->courier_id = $request->courier_id > 0 ? $request->courier_id : -1;

        $items = [];
        foreach ($request->products as $productData) {
            $product = Product::find($productData['product_id']);
            if (!$product) {
                return response()->json(['status' => "ERR", 'message' => 'Ürün bulunamadı: ' . $productData['product_id']]);
            }

            $items[] = [
                "price" => $product->price,
                "unitSellingPrice" => $product->price,
                "items" => array_fill(0, $productData["quantity"], ["packageItemId" => "ahtaPOS"]),
                "productId" => $product->id,
                "name" => $product->name,
            ];
        }

        $order->items = json_encode($items);
        $order->status = "PENDING";

        try {
            $order->save();
        } catch (\Exception $e) {
            \Log::error('Order creation error: ' . $e->getMessage());
            return response()->json(['status' => "ERR", 'message' => 'Sipariş kaydedilirken bir hata oluştu.']);
        }

        $printed = $this->generateInvoice($order);

        return response()->json(['status' => "OK", 'printed' => $printed]);
    }


    private function generateInvoice($order)
    {
        $date = now()->format("d.m.Y H:i:s");

        return view('invoice-template', compact('order', 'date'))->render();  // Fatura görünümü burada oluşturulacak
    }

    ////////////////////////////////// Old Update Order Status Function //////////////////////////////////

    // public function updateOrderStatus(Request $request)
    // {
    //     $order = Order::where('tracking_id', $request->tracking_id)->first();

    //     $order->status = $request->action;
    //     $order->message = $request->message;
    //     $s = $order->save();

    //     if ($s) {
    //         return response()->json(['status' => "OK"]);
    //     } else {
    //         return response()->json(['status' => "ERR"]);
    //     }
    // }

    public function updateOrderStatus(Request $request)
    {
        // İstekten gelen veriler
        $trackingId = $request->input('tracking_id');
        $action = $request->input('action');
        $message = $request->input('message');

        // Siparişi bul
        $order = Order::where('tracking_id', $trackingId)->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        // Siparişin durumunu ve mesajını güncelle
        $order->status = $action;
        $order->message = $message;
        $saveStatus = $order->save();

        // Eğer sipariş durumu DELIVERED veya UNSUPPLIED ise kuryeyi aktif hale getir
        if ($action == 'DELIVERED' || $action == 'UNSUPPLIED') {
            // Siparişle ilişkilendirilmiş kurye siparişini bul
            $courierOrder = CourierOrder::where('order_id', $order->id)->first();
            if ($courierOrder) {
                // Kuryeyi bul ve durumunu aktif olarak güncelle
                $courier = Courier::find($courierOrder->courier_id);
                if ($courier) {
                    $courier->situation = 'Aktif';
                    $courier->save();
                }
            }
        }

        // Güncelleme işlemi başarılıysa yanıt ver
        if ($saveStatus) {
            return response()->json(['status' => "OK"]);
        } else {
            return response()->json(['status' => "ERR"]);
        }
    }


    public function printed($id)
    {
        $order = Order::where('id', $id)->first();
        $customer_address = CustomerAddress::where('customer_id', $order->customer_id)->first();

        $printed = '
                <style>
                    body{
                      font-family: Arial;
                      text-align: left;
                      font-size: 14px;
                    }
                    .restaurant{
                      font-weight: bold;
                      text-align: center;
                      padding-bottom: 5px;
                    }

                    .adres{
                      font-weight: bold;
                    }

                    .order_time{
                      font-weight: bold;
                    }
                    .dot{
                      border-bottom: 1px dotted;
                      padding-top: 10px;
                      padding-bottom: 10px;
                    }
                    .name{
                      padding: 10px 0px 20px;
                    }
                    .item{
                      font-weight: bold;
                    }
                    .tabletitle{
                      padding-top: 10px;
                    }
                    .legalcopy{
                      text-align: center;

                    }
                  </style>

             <div id="invoice-POS">
 
                  <div class="logo"></div>
                  <div class="restaurant"> 
                    ' . Auth::user()->name . '
                  </div><!--End Info-->
                  <div class="adres">
                     <b>Restoran Adresi:</b>' . Auth::user()->address . '
                  </div>
                   <div class="adres">
                    <b>Restoran İletişim:</b>' . Auth::user()->phone . '
                  </div>
                  <div class="order_time">
                    Sipariş Zamanı: ' . Carbon::parse($order->created_at)->format('d.m.Y H:i:s') . '
                  </div> 
                  <div class="trackingid">
                    <b>Sipariş No: </b>' . $order->tracking_id . '
                  </div>
                     <div class="dot"></div>
                  
                  <div class="name"><b>Müşteri Adı:</b>' . $order->full_name . '</div>
                  <div class="adress">
                  <b>Adres:</b>' . $order->address . '
                  </div>
                  <div class="adress">
                   ' . $order->notes . '
                  </div>
                  <div class="adress">
                   <b>Müşteri İletişim Numarası: ' . $order->phone . '</b>
                  </div>
                   
                  <div class="dot"></div>


                  <div id="bot">

                      <div id="table"  style="padding-top: 20px;">
                          <table>
                              <tr class="tabletitle">
                                  <td class="item">Adet</td> 
                                  <td class="item">Yemek</td> 
                                  <td class="item">Fiyat</td> 
                                  <td class="item">Tutar</td> 
                              </tr>
                              ';
        $items = json_decode($order->items);

        if ($order->platform == "yemeksepeti") {


            foreach ($items as $item) {
                $printed .=  '<tr class="service">
                                          <td class="tableitem"><p class="itemtext">' . $item->amount . '</p></td>
                                          <td class="tableitem"><p class="itemtext">' . $item->name . '</p></td>
                                          <td class="tableitem"><p class="itemtext">' . $item->price . ' TL</p></td>
                                          <td class="tableitem"><p class="itemtext">' . $item->total . ' TL</p></td>
                                      </tr> ';
            }
        } elseif ($order->platform == "getir") {

            foreach ($items as $item) {
                $printed .=  '<tr class="service">
                                          <td class="tableitem"><p class="itemtext">' . $item->amount . '</p></td>
                                          <td class="tableitem"><p class="itemtext">' . $item->name . '</p></td>
                                          <td class="tableitem"><p class="itemtext">' . $item->price . ' TL</p></td>
                                          <td class="tableitem"><p class="itemtext">' . (int)$item->amount * (int)$item->price . ' TL</p></td>
                                      </tr> ';
            }
        } else {

            foreach ($items as $item) {
                $printed .=  '<tr class="service">
                                          <td class="tableitem"><p class="itemtext">' . count($item->items) . '</p></td>
                                          <td class="tableitem"><p class="itemtext">' . $item->name . '</p></td>
                                          <td class="tableitem"><p class="itemtext">' . $item->price . ' TL</p></td>
                                          <td class="tableitem"><p class="itemtext">' . (int)count($item->items) * (int)$item->price . ' TL</p></td>
                                      </tr> ';
            }
        }

        $printed .= '
                            
                              <tr class="tabletitle">
                                  <td></td>
                                  <td></td>
                                  <td class="item">Toplam:</td>
                                  <td class="payment">' . $order->amount . ' TL</td>
                              </tr>
             
             

                          </table>
                      </div><!--End Table-->
                       <div class="dot"></div>
                      
                       <div class="adress" style="padding-top: 20px;">
                        <span class="item">Ödeme Şekli:</span> ' . $order->payment_method . '.<br> 
                        

                      </div>
                      <div class="dot"></div>

                      <div class="legalcopy">
                          <p class="legal"><strong></strong> 
                          <br>
                          <center>
                          <br>
                          <b>- EsnafExpress Bizi Tercih Ettiğiniz İçin Teşekkür Ederiz.</b>
                          </center>


                          </p>
                      </div>

                    </div>
              </div>';

        return response()->json(['printed' => $printed]);
    }

    public function deleteOrder($id)
    {
        $order = Order::where('id', $id) - delete();


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