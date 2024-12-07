<?php

namespace App\Helpers;

class OrdersHelper {
    public static function addressReplace($address) {
       if ($address){
           $keyword = "Notes:";
           $position = strpos($address, $keyword);

           if ($position !== false) {
               // "Notes:" kelimesinden önceki kısmı alıyoruz
               $address = substr($address, 0, $position);
           }

           return $address;
       }else{return null;}
    }
}
