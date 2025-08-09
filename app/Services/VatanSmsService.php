<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class VatanSmsService
{
    public function sendSms($phone, $message)
    {
        $restaurant = Auth::guard('restaurant')->user();

        header('Content-Type: text/html; charset=utf-8');
        $postUrl = 'http://panel.vatansms.com/panel/smsgonder1Npost.php';
        $customerNo = $restaurant->vatan_sms_customer;
        $username = $restaurant->vatan_sms_username;
        $password = $restaurant->vatan_sms_password;
        $orginator = $restaurant->vatan_sms_orginator;

        $TUR = 'Turkce';  // Normal yada Turkce
        $time = date('Y-m-d H:i:s', strtotime(Carbon::now()));
        $mesaj1 = $message;
        $numara1 = $this->phoneConvert($phone);

        $xmlString = 'data=<sms>
<kno>' . $customerNo . '</kno>
<kulad>' . $username . '</kulad>
<sifre>' . $password . '</sifre>
<gonderen>' . $orginator . '</gonderen>
<mesaj>' . $mesaj1 . '</mesaj>
<numaralar>' . $numara1 . '</numaralar>
<tur>' . $TUR . '</tur>
</sms>';

// Xml içinde aşağıdaki alanlarıda gönderebilirsiniz.
//<zaman>'. $time.'</zaman> İleri tarih için kullanabilirsiniz

        $Veriler = $xmlString;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $postUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $Veriler);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    function phoneConvert($numara) {
        // Tüm rakam dışı karakterleri temizle
        $sadeceRakam = preg_replace('/\D/', '', $numara);

        // Başındaki +90, 90 veya 0'ı temizle
        if (substr($sadeceRakam, 0, 2) === '90') {
            $sadeceRakam = substr($sadeceRakam, 2);
        } elseif (substr($sadeceRakam, 0, 1) === '0') {
            $sadeceRakam = substr($sadeceRakam, 1);
        }

        // 10 haneli bir cep numarası mı kontrol et
        if (strlen($sadeceRakam) === 10) {
            return $sadeceRakam;
        } else {
            return false; // Hatalı format
        }
    }
}
