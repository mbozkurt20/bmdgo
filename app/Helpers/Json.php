<?php

namespace App\Helpers;
class Json
{
    static function success(string $message,$data = [], int $code = 200)
    {
        return response()->json(['message'=> $message,'data' => $data,'code'=>$code], $code);
    }
    static function error(string $message, int $code = 400){
        return response()->json(['message'=>$message,'code' => $code],$code);
    }
}
