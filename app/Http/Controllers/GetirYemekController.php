<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\Order;
use App\Models\Admin;

class GetirYemekController extends Controller
{
   public function index()
	{
		$restaurants = Restaurant::where('getir_restaurant_secret_key','!=','')->get();
     	
 		foreach($restaurants as $restaurant){
          
        	if ($restaurant->getir_token == null && $restaurant->getir_restaurant_id == "") {
				
            	$loginData = $this->login($restaurant->getir_app_secret_key, $restaurant->getir_restaurant_secret_key,  $restaurant->id);

            } else {
				 
                $loginData["getir_token"] = $restaurant->getir_token;
               
            }


          $this->orders($loginData["getir_token"], $restaurant->getir_restaurant_id,  $restaurant->id);
 
        }
	}


	private function login($getir_app_secret_key, $getir_restaurant_secret_key, $restaurant_id)
	{
		$url = 'https://food-external-api-gateway.getirapi.com/auth/login';

        $data = array(
            "appSecretKey" => $getir_app_secret_key,
            "restaurantSecretKey" => $getir_restaurant_secret_key
        );

        $postdata = json_encode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $result = curl_exec($ch); 

        $resultd = json_decode($result); 
 
        $loginData["getir_token"] = $resultd->token;
        $loginData["getir_restaurant_id"] = $resultd->restaurantId;

        Restaurant::where('id', $restaurant_id)->update($loginData);

        curl_close($ch);

        return $loginData;
	}
  
  	private function orders($token, $restaurant_id, $k_restaurant_id)
	{
		 if($token){
         		$curl = curl_init();
			curl_setopt_array($curl, array(
			  //CURLOPT_URL => 'https://food-external-api-gateway.getirapi.com/food-orders/report/details?restaurantIds='.$restaurant_id.'&startDate=2023-12-10&endDate=2023-12-10',
			  CURLOPT_URL => 'https://food-external-api-gateway.getirapi.com/food-orders/active',
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'POST',
			  CURLOPT_HTTPHEADER => array(
			    'Accept: application/json',
			    'token: '.$token
			  ),
			));

			$response = curl_exec($curl);

			curl_close($curl);

			$getorder = json_decode($response);
           
           
			if(isset($getorder->code) && $getorder->code == 7){

				$loginData["getir_token"] = null;
        		$loginData["getir_restaurant_id"] = null;

				Restaurant::where('id', $k_restaurant_id)->update($loginData);
			}
			
       
            
           
           if($getorder){


			    foreach ($getorder as $row) {  
                  
                     if(isset($row->client)){
                       $info = $row->client;
                     }else{
                       $info = [
                         'deliveryAddress' => 'yok',
                         'clientPhoneNumber' => 'yok',
                         'name' => 'yok'
                       ];
                     }
                  
			    	
			    	$address = $info->deliveryAddress;
			    	$payment = $row->paymentMethodText;
			    	$products = $row->products;
                  
                    if(isset($row->totalDiscountedPrice)){
                    	$amount = $row->totalDiscountedPrice;
                    }else{
                    	$amount = $row->totalPrice;
                    }
                    
                     if($payment->tr == "Online Ödeme"){
                		$payment_tr = "Online Kredi/Banka Kartı";
           			 }else{
                        $payment_tr = $payment->tr;
                      }

                  	$orderData = [
                      'platform'       => 'getir',
                      'courier_id'     => 0,
                      'status'         => 'PENDING',
                      'restaurant_id'  => $k_restaurant_id,
                      'tracking_id'    => $row->id,
                      'full_name'      => $info->name,
                      'phone'          =>  $info->clientPhoneNumber,
                      'amount'         => $amount,
                      'payment_method' => $payment_tr,
                      'items'          => json_encode($products),
                      'address'        => $address->address,
                      'notes'          => $address->description,
                      'confirmationId' => $row->confirmationId
                  ];
                  
                   $order = Order::where('tracking_id', $row->id)->first();

                  if (!$order) {
                      Order::create($orderData);
                  } 

                  	$auto = Admin::find(1);
                    if ($auto->auto_orders == 1) {
                        $courie = Courier::where('restaurant_id', 0)->where('situation', 'Aktif')->first();
                        if ($courie) {
                            $new = new CourierOrder();
                            $new->courier_id = $courie->id;
                            $new->order_id = $order->id;
                            $new->save();

                            $courie->situation = "Serviste";
                            $courie->save();
                        }
                    }
                    
                 
 

			    }
           }

          
           
         }

			 

	} 

 
}
