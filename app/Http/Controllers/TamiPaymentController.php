<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\City;
use App\Models\District;
use App\Models\TopupMovement;
use App\Services\Tami\JWTSignatureGenerator;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TamiPaymentController extends Controller
{
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * 1. Ödeme formunu göster
     */
    public function showForm(Request $req)
    {
        $topup = $req->top_up;
        $amount = auth()->user()->top_up_price * $topup;

        return view('admin.payment.tami.form',compact('topup','amount'));
    }

    /**
     * 2. Ödeme işlemini başlat
     */
    public function start(Request $request)
    {
        $orderId = $this->generateGuid();
        $amount = $request->amount;
        $admin = Auth::guard('admin')->user();

        // ENV değerlerini al
        $endpoint    = env('TAMI_ENDPOINT', 'https://sandbox-paymentapi.tami.com.tr');
        $merchantId  = env('TAMI_MERCHANT_ID', '77006950');
        $terminalId  = env('TAMI_TERMINAL_ID', '84006953');
        $secretKey   = env('TAMI_SECRET_KEY', 'your-secret');
        $kid         = env('TAMI_FIXED_KID', '00ff6ea8-3511-4d04-946c-ba569208306f');
        $k           = env('TAMI_FIXED_K', '87919a8f-957b-427b-ae12-167622ab52b5');

        $callbackUrl = route('payment.callback');

        $payload = [
            "callbackUrl"      => $callbackUrl,

            "currency"         => "TRY",
            "installmentCount" => 1,
            "motoInd"          => false,
            "paymentGroup"     => "PRODUCT",
            "paymentChannel"   => "WEB",
            "card" => [
                "holderName"  => $request->card_name,
                "cvv"         => $request->cvc,
                "expireMonth" => (int)$request->expire_month,
                "expireYear"  => (int)$request->expire_year,
                "number"      => $request->card_number,
            ],
            "billingAddress" => [
                "address"      => $admin->address??"",
                "city"         => $admin->city_id ? City::find($admin->city_id)->name:"",
                "companyName"  => $admin->name,
                "country"      => "Türkiye",
                "district"     => $admin->district_id ? District::find($admin->district_id)->name:"",
                "contactName"  => $admin->name??" ",
                "phoneNumber"  => $admin->phone??" ",
                "zipCode"      => "34846"
            ],
            "shippingAddress" => [
                "address"      => $admin->address??"",
                "city"         =>$admin->city_id ? City::find($admin->city_id)->name:"",
                "companyName"  => $admin->name??" ",
                "country"      => "Türkiye",
                "district"     => $admin->district_id ? District::find($admin->district_id)->name:"",
                "contactName"  => $admin->name??" ",
                "phoneNumber"  => $admin->phone??" ",
                "zipCode"      => "34846"
            ],
            "buyer" => [
                "ipAddress"          => $request->ip(),
                "buyerId"            => uniqid(),
                "name"               => $admin->name??" ",
                "surName"            => $admin->name??" ",
                "identityNumber"     => 11111111111,
                "city"               => $admin->city_id ? City::find($admin->city_id)->name:"",
                "country"            => "Türkiye",
                "zipCode"            => "34846",
                "emailAddress"       => $admin->email??" ",
                "phoneNumber"        => $admin->phone??" ",
                "registrationAddress"=> "Maltepe",
                "lastLoginDate"      => "2023-08-04T11:58:35.822",
                "registrationDate"   => "2023-07-25T11:58:35.822"
            ],
            "orderId" => $orderId,
            "amount"  => $amount,
        ];

        // Security Hash ekle
        $payload['securityHash'] = JWTSignatureGenerator::generateJWKSignature(
            $merchantId,
            $terminalId,
            $secretKey,
            json_encode($payload),
            $kid,
            $k
        );

        $headers = [
            'Content-Type'    => 'application/json',
            'Accept-Language' => 'tr',
            'PG-Api-Version'  => 'v2',
            'PG-Auth-Token'   => $this->generateAuthToken($merchantId, $terminalId, $secretKey),
            'correlationId'   => $this->generateGuid()
        ];

        $response = $this->client->post($endpoint.'/payment/auth', [
            'headers' => $headers,
            'json'    => $payload,
            'verify'  => false, // sandbox için
        ]);

        $data = json_decode($response->getBody(), true);

        $topUp = TopupMovement::create([
            'admin_id'          => $admin->id,
            'top_up_price'      => $admin->top_up_price,
            'top_up'            => $request->topup,
            'type'              => 'yükleme',
            'is_approved'       => 0,
            'total_amount'      => $request->amount,
            'created_by_user_id'=> Auth::guard('admin')->id(),
            'created_type'      => 'admin',
            'order_id'          => $data['orderId'],
            'payment_details'   => json_encode([])
        ]);

        if ($topUp){
            return view('admin.payment.tami.3ds', [
                'threeDSHtmlContent' => $data['threeDSHtmlContent'] ?? ''
            ]);
        }else {
            return view('admin.payment.form')->with(['test' => 'Ödeme Sırasında Bir Sorun MEydana Geldi']);
        }
    }

    public function callback(Request $request)
    {
        try {
            // Önce orderId ile kaydı bul
            $orderId = $request->input('orderId');

            $successUrl = route('payment.success') . '?order_id=' . $orderId . '&status=success';
            return redirect($successUrl);
        } catch (\Exception $e) {
            Log::error('Tami callback hatası: ' . $e->getMessage());
            $orderId = $request->input('orderId', 'unknown');
            $failUrl = route('payment.fail') . '?order_id=' . $orderId . '&status=error';
            return redirect($failUrl);
        }
    }

    public function successPage(Request $request)
    {
        $orderId = $request->input('order_id');

        $topUp = TopupMovement::where('order_id', $orderId)->first();

        if (!$topUp) {
            Log::error('TopupMovement bulunamadı: ' . $orderId);
            // Session olmadan hata yönetimi
            return response()->json(['error' => 'Ödeme kaydı bulunamadı.'], 404);
        }

        // Tami API sorgulama işlemleri...
        $endpoint = env('TAMI_ENDPOINT');
        $merchantId = env('TAMI_MERCHANT_ID');
        $terminalId = env('TAMI_TERMINAL_ID');
        $secretKey = env('TAMI_SECRET_KEY');
        $kid = env('TAMI_FIXED_KID');
        $k = env('TAMI_FIXED_K');

        $payload = [
            'orderId' => $orderId,
            'isTransactionDetail' => false,
        ];

        $payload['securityHash'] = JWTSignatureGenerator::generateJWKSignature(
            $merchantId, $terminalId, $secretKey, json_encode($payload), $kid, $k
        );

        $headers = [
            'Content-Type' => 'application/json',
            'Accept-Language' => 'tr',
            'PG-Api-Version' => 'v2',
            'PG-Auth-Token' => $this->generateAuthToken($merchantId, $terminalId, $secretKey),
            'correlationId' => $this->generateGuid()
        ];

        $response = $this->client->post($endpoint.'/payment/query', [
            'headers' => $headers,
            'json' => $payload,
            'verify' => false,
        ]);

        $data = json_decode($response->getBody(), true);

        // Ödeme kaydını güncelle
        $topUp->update([
            'is_approved' => 1,
            'is_paid' => 1,
            'payment_details' => json_encode([
                'orderDate' => $data['orderDate'],
                'amount' => $data['amount'],
                'currency' => $data['currency'],
                'orderId' => $data['orderId'],
                'is3D' => $data['is3D'],
                'card' => $data['card'],
            ])
        ]);

        // Admin bakiyesini güncelle
        if ($topUp->is_approved) {
            $admin = Admin::find($topUp->admin_id);
            if ($admin) {
                $admin->increment('top_up_balance', $topUp->top_up);
            }
        }

        if (!$topUp) {
            return redirect()->route('admin.index')->with('error', 'Ödeme kaydı bulunamadı.');
        }

        return view('admin.payment.tami.success', [
            'message' => 'Ödeme başarılı! ' . $topUp->top_up . ' TL yüklendi.',
            'orderId' => $orderId,
            'amount' => $topUp->total_amount
        ]);
    }

    public function failPage(Request $request)
    {
        $orderId = $request->input('order_id');

        $topUp = TopupMovement::where('order_id', $orderId)->first();

        $topUp->delete();

        return view('admin.payment.tami.fail', [
            'message' => 'Ödeme işlemi başarısız oldu.',
        ]);
    }

    private function generateAuthToken(string $merchantId, string $terminalId, string $secretKey): string
    {
        return $merchantId . ":" . $terminalId . ":" . base64_encode(
                hash('sha256', $merchantId.$terminalId.$secretKey, true)
            );
    }

    private function generateGuid(): string
    {
        return bin2hex(random_bytes(16));
    }
}
