<?php

namespace App\Services\Paytr;

class PayTrService
{
    public function getToken($userName,$userAddress,$userPhone, $email, $payment_amount, $basket)
    {
        $merchant_id    = config('payment.paytr.merchant_id');
        $merchant_key   = config('payment.paytr.merchant_key');
        $merchant_salt  = config('payment.paytr.merchant_salt');
        $test_mode      = (int) config('payment.paytr.sandbox'); // 1=test, 0=live

        $user_ip = request()->ip();
        $merchant_oid = uniqid();
        $no_installment = 0;
        $max_installment = 0;
        $currency = "TL";

        $basket = base64_encode(json_encode($basket, JSON_UNESCAPED_UNICODE));

        $hash_str = $merchant_id.$user_ip.$merchant_oid.$email.$payment_amount.$basket.$no_installment.$max_installment.$currency.$test_mode;
        $paytr_token = base64_encode(hash_hmac('sha256', $hash_str.$merchant_salt, $merchant_key, true));

        $post_vals = [
            'merchant_id'         => $merchant_id,
            'user_ip'             => $user_ip,
            'merchant_oid'        => $merchant_oid,
            'email'               => $email,
            'payment_amount'      => $payment_amount,
            'paytr_token'         => $paytr_token,
            'user_basket'         => $basket,
            'no_installment'      => $no_installment,
            'max_installment'     => $max_installment,
            'user_name'           => $userName,
            'user_address'        => $userAddress,
            'user_phone'          => $userPhone,
            'merchant_ok_url'     => route('paytr.success'),
            'merchant_fail_url'   => route('paytr.fail'),
            'merchant_notify_url' => route('paytr.callback'), // ✅ doğru parametre
            'currency'            => $currency,
            'test_mode'           => $test_mode,
            'iframe_v2'           => 1,
            'iframe_v2_dark'      => 0,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.paytr.com/odeme/api/get-token");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_vals);
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result, true);
    }
}
