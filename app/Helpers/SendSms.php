<?php

namespace App\Helpers;

use App\Services\VatanSmsService;
use Illuminate\Support\Facades\Auth;

class SendSms {
    static function send($phone,$message,$adminId)
    {
        if ($adminId != null) {
            try {
                $smsService = new VatanSmsService();
                $result = $smsService->sendSms($phone, $message,$adminId);
                return response()->json($result);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        return false;
    }
}
