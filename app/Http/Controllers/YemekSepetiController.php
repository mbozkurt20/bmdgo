<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Restaurant;
use App\Models\Order;
use App\Jobs\AssignOrderToCourier;

class YemekSepetiController extends Controller
{
    public function index()
    {
        $restaurants = Restaurant::where('yemeksepeti_email', '!=', '')->get();

        foreach ($restaurants as $restaurant) {

            if ($restaurant->yemeksepeti_tarih != date("Y-m-d") || $restaurant->yemeksepeti_token == "") {
                $loginData = $this->login($restaurant->id, $restaurant->yemeksepeti_email, $restaurant->yemeksepeti_password);
            } else {
                $loginData["yemeksepeti_token"] = $restaurant->yemeksepeti_token;
            }

            if (!isset($loginData["yemeksepeti_token"])) {
                continue;
            }

            $this->orders($restaurant->id, $loginData["yemeksepeti_token"]);
        }


        die;
    }

    private function login($restaurant_id, $email, $password)
    {
        $loginData = [];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://vendor-api-tr.me.restaurant-partners.com/api/1/auth/form");
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array(
            'password' => $password,
            'username' => $email,
        ));
        $data = curl_exec($ch);
        curl_close($ch);
        $data_obj = json_decode($data);

        if (!isset($data_obj->token)) {
            return $loginData;
        }

        $access_token = $data_obj->token;

        $loginData["yemeksepeti_token"] = $access_token;
        $loginData["yemeksepeti_tarih"] = date("Y-m-d");

        Restaurant::where('id', $restaurant_id)->update($loginData);


        return $loginData;
    }

    private function orders($restaurant_id, $token)
    {
        $headers = array(
            "Content-Type: application/json",
            "origin: https://web-tr.me.restaurant-partners.com",
            "authorization: Bearer $token",
            "referer: https://web-tr.me.restaurant-partners.com/",
            "sec-ch-ua: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0",
            "sec-ch-ua-mobile: ?0",
            "sec-ch-ua-platform: macOS",
            "sec-fetch-dest: empty",
            "sec-fetch-mode: cors",
            "sec-fetch-site: same-site",
            "user-agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0",
        );

        $bugun = date("Y-m-d");
        $cevir = strtotime('-1 day', strtotime($bugun));
        $cevir2 = strtotime('+1 day', strtotime($bugun));
        $dun = date("Y-m-d", $cevir);
        $yarin = date("Y-m-d", $cevir2);
        $url = "https://vendor-api-tr.me.restaurant-partners.com/api/2/deliveries?from=" . $bugun . "T00:00:00.000Z&to=" . $bugun . "T23:59:59.000Z";

        $post_params = array(
            'from'     => $dun . "T00:00:00.000Z",
            'to'       => $yarin . "T23:59:59.000Z",
            'statuses' => "ACCEPTED",
            'statuses' => "PREORDER_ACCEPTED",
        );

        $payload = json_encode($post_params);

        $ch2 = curl_init($url);
        curl_setopt($ch2, CURLOPT_POST, true);
        curl_setopt($ch2, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch2, CURLOPT_POST, $payload);
        curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_FAILONERROR, true);


        $response = curl_exec($ch2);
        
        if (curl_errno($ch2)) {
            $error_msg = curl_error($ch2);
            echo $error_msg;
        }

        $content = json_decode($response);

        foreach ($content as $row) {
            
            $customer = $row->customer;
            $payments = $row->payment;

            if(isset($row->address)){

                $address = $row->address;

           

                $orderAddress = '';

                if(isset($row->address->city)){
                    $orderAddress.= $row->address->city. " ";
                }

                if(isset($row->address->street)){
                    $orderAddress.= $row->address->street. " ";
                }
                    
                if(isset($row->address->building)){
                    $orderAddress.= "Bina No:". $row->address->building." ";
                }

                if(isset($row->address->floor)){
                    $orderAddress.= "Kat:".$row->address->floor." ";
                }

                if(isset($row->address->entrance) ){
                    $orderAddress.= "Kapı No:". $row->address->entrance." ";
                }
                    
                if(isset($row->address->info)){
                    $orderAddress.= $row->address->info. " ";
                }

                if(isset($row->address->area)){
                    $orderAddress.= "Bölge ".$row->address->area;
                }


            }else{

                $orderAddress = '';

            }
            
            
            if ($payments->total) {
                $amount = $payments->total;
            } else {
                $amount = $payments->itemsTotalPrice;
            }


            if($payments->paymentMethod == "Nakit"){
                $payment = "Kapıda Nakit ile Ödeme";
            }elseif($payments->paymentMethod == "Kapıda Kredi/Banka Kartı"){
                $payment = "Kapıda Kredi Kartı ile Ödeme";
            }else{
                $payment = $payments->paymentMethod;
            }
            

            if(isset($customer->lastName)){
                $last_name = $customer->lastName;
            }else{
                $last_name = "";
            }
          

            $orderData = [
                'platform'       => 'yemeksepeti',
                'courier_id'     => 0,
                'restaurant_id'  => $restaurant_id,
                'tracking_id'    => $row->id,
                'full_name'      => $customer->firstName . " " . $last_name,
                'phone'          => $customer->phone,
                'amount'         => $amount,
                'status'         => "PENDING",
                'payment_method' => $payment,
                'items'          => json_encode($row->items),
                'address'        => $orderAddress,
            ];

           

            $order = Order::where('tracking_id', $row->id)->first();

            if (!$order) {
                $order = Order::create($orderData);
            }

            AssignOrderToCourier::dispatch($order);
        }
    }
}
