<?php

namespace App\Helpers;

use App\Services\VatanSmsService;
use Illuminate\Support\Facades\Auth;

class SendSms {
    static function send($phone,$message)
    {
        if (Auth::guard('restaurant')->check() && Auth::guard('restaurant')->user()->is_sms) {
            try {
                $smsService = new VatanSmsService();
                $result = $smsService->sendSms($phone, $message);
                return response()->json($result);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        return false;
    }
}
