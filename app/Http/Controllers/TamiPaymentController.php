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
        $amount = \auth()->user()->top_up_price * $topup;

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
                "buyerId"            => "dc5d150f19ac475289c78bb24acc56bb",
                "name"               =>  $admin->name??" ",
                "surName"            =>  $admin->name??" ",
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

    /**
     * 3. Callback → Başarılı
     */
    public function callback(Request $request)
    {
        $endpoint    = env('TAMI_ENDPOINT', 'https://sandbox-paymentapi.tami.com.tr');
        $merchantId  = env('TAMI_MERCHANT_ID', '77006950');
        $terminalId  = env('TAMI_TERMINAL_ID', '84006953');
        $secretKey   = env('TAMI_SECRET_KEY', 'your-secret');
        $kid         = env('TAMI_FIXED_KID', '00ff6ea8-3511-4d04-946c-ba569208306f');
        $k           = env('TAMI_FIXED_K', '87919a8f-957b-427b-ae12-167622ab52b5');

        // Body
        $payload = [
            'orderId'            => $request->input('orderId'),
            'isTransactionDetail'=> false,
        ];

        // Security hash ekle
        $payload['securityHash'] = JWTSignatureGenerator::generateJWKSignature(
            $merchantId,
            $terminalId,
            $secretKey,
            json_encode($payload),
            $kid,
            $k
        );

        // Headers
        $headers = [
            'Content-Type'    => 'application/json',
            'Accept-Language' => 'tr',
            'PG-Api-Version'  => 'v2',
            'PG-Auth-Token'   => $this->generateAuthToken($merchantId, $terminalId, $secretKey),
            'correlationId'   => $this->generateGuid()
        ];

        // Guzzle POST
        $response = $this->client->post($endpoint.'/payment/query', [
            'headers' => $headers,
            'json'    => $payload,
            'verify'  => false, // sandbox için
        ]);

        $data = json_decode($response->getBody(), true);

        $topUp = TopupMovement::where('order_id', $request->input('orderId'))->first();

        $topUp->update([
            'is_approved' => 1,
            'is_paid' => 1,
            'payment_details'   => json_encode([
                'success' => $data['success'],
                'systemTime' => $data['systemTime'],
                'amount' => $data['amount'],
                'orderDate' => $data['orderDate'],
                'currency' => $data['currency'],
                'installmentCount' => $data['installmentCount'],
                'card' => $data['card'],
            ])
        ]);

        if ($topUp) {
            $admin = Admin::where('id',$topUp->admin_id)->first();
            $admin->increment('top_up_balance', $topUp->top_up);
        }

        return response()->json(['status'=>'ok']);
    }

    public function successPage()
    {
        dd(12215);
        return view('admin.payment.tami.success', [ 'message' => 'Ödeme Başarılı!']);
    }

    public function failPage()
    {

        return view('admin.payment.tami.fail', [ 'message' => 'Ödeme Başarısız!']);
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
