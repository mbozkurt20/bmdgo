<?php

namespace App\Http\Controllers;

use App\Helpers\GeoLocation;
use App\Models\Admin;
use App\Models\City;
use App\Models\Courier;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\District;
use App\Models\Expenses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $customers = Customer::where('status', 'active')->where('restaurant_id', Auth::user()->id)->get();
        return view('restaurant.customers.index', compact('customers'));
    }

    public function new()
    {
        $cities = DB::table('cities')->get();
        return view('restaurant.customers.new', compact('cities'));
    }

    public function getCustomers()
    {
        try {
            $customers = \App\Models\Customer::where('restaurant_id', auth()->id())->get();

            return response()->json([
                'success' => true,
                'customers' => $customers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Müşteriler yüklenirken hata oluştu: ' . $e->getMessage()
            ]);
        }
    }

    public function edit($id)
    {
        $customer = Customer::find($id);
        return view('restaurant.customers.edit', compact('customer'));
    }

    public function create(Request $request)
    {
        $testMode = env('TEST_MODE');

        if ($testMode) {
            if (Customer::count() > env('TEST_MODE_LIMIT')) {
                return redirect()->back()->with('test', 'Test Modu: Üzgünüz, En Fazla '.env('TEST_MODE_LIMIT').' Kayıt Ekleyebilirsiniz');
            }
        }

        // Save customer information
        $create = new Customer();
        $create->restaurant_id = Auth::user()->id; // Assuming the authenticated user is the restaurant
        $create->name = $request->input('name');
        $create->phone = $request->input('phone');
        $create->mobile = $request->input('mobile');
        $create->email = $request->input('email')??null;
        $create->save();

        $city =  City::find(Admin::find(auth()->user()->admin_id)->city_id);

        // Check if address data is present
        if ($request->address) {
            $errors = [];
            foreach ($request->address as $adres) {

                $addres = $adres['mahalle'] . ' mah. ' .
                    $adres['sokak_cadde'] . ' sokak. Bina No:' .
                    $adres['kat'] . ' Kat:' .
                    $adres['daire_no'] . ' Daire No:' .
                    $adres['bina_no'] . ' Bina no' .
                    District::find($adres['ilce'])->name . '/' .$city->name. ' Türkiye';

                $location = GeoLocation::getLatLong($addres);


                if (!isset($location['error'])) {
                    // Save each address for the customer
                    $address = new CustomerAddress();
                    $address->customer_id = $create->id;
                    $address->restaurant_id = Auth::user()->id;
                    $address->name = $adres['name'];
                    $address->sokak_cadde = $adres['sokak_cadde'];
                    $address->bina_no = $adres['bina_no'];
                    $address->city_id = $city->id;
                    $address->district_id = $adres['ilce'];
                    $address->kat = $adres['kat'];
                    $address->latitude = $location['lat'];
                    $address->longitude = $location['lon'];
                    $address->daire_no = $adres['daire_no'];
                    $address->mahalle = $adres['mahalle'];
                    $address->adres_tarifi = $adres['adres_tarifi'] ?? '';
                    $address->save();
                } else {
                    // Eğer konum bulunamadıysa hata kaydet
                    $errors[] = [
                        'input' => $location['input'],
                        'message' => $location['error']
                    ];
                }
            }

            if (!empty($errors)) {
                // Hata varsa kullanıcıyı editlemeye yönlendir
                return redirect()->route('restaurant.customers.edit', $create->id)
                    ->with('test', 'Bazı adreslerin konumu bulunamadı.')
                    ->with('errors', $errors);
            }
        }

        return redirect()->back()->with('message', 'Müşteri Başarıyla Eklendi.');
    }

    public function update(Request $request)
    {
        $customer = Customer::find($request->id);
        $customer->name = $request->name;
        $customer->phone = $request->phone;
        $customer->mobile = $request->mobile;
        $customer->save();
        $city =  City::find(Admin::find(auth()->user()->admin_id)->city_id);

        if ($request->address) {
            // Mevcut adresleri çek
            $existingAddresses = CustomerAddress::where('customer_id', $customer->id)->pluck('id')->toArray();

            // Request’ten gelen id’leri topla
            $requestIds = collect($request->address)->pluck('id')->filter()->toArray();

            // Silinmesi gereken adresler (veritabanında var ama request'te yok)
            $toDelete = array_diff($existingAddresses, $requestIds);
            CustomerAddress::whereIn('id', $toDelete)->delete();

            foreach ($request->address as $adres) {
                $addres = $adres['mahalle'] . ' mah. ' .
                    $adres['sokak_cadde'] . ' sokak. Bina No:' .
                    $adres['kat'] . ' Kat:' .
                    $adres['daire_no'] . ' Daire No:' .
                    $adres['bina_no'] . ' Bina no ' .
                    District::find($adres['ilce'])->name . '/' .$city->name. ' Türkiye';

                $location = GeoLocation::getLatLong($addres);

                if (isset($location['error'])) {
                    return response()->json(['message' => 'Konumu doğru girip tekrar deneyiniz.']);
                }

                if (isset($adres['id']) && $findAddress = CustomerAddress::find($adres['id'])) {
                    // Güncelle
                    $findAddress->update([
                        'name' => $adres['name'],
                        'sokak_cadde' => $adres['sokak_cadde'],
                        'bina_no' => $adres['bina_no'],
                        'kat' => $adres['kat'],
                        'daire_no' => $adres['daire_no'],
                        'mahalle' => $adres['mahalle'],
                        'adres_tarifi' => $adres['adres_tarifi'],
                        'latitude' => $location['lat'],
                        'longitude' => $location['lon'],
                    ]);
                } else {
                    // Yeni ekle
                    CustomerAddress::create([
                        'restaurant_id' => Auth::user()->id,
                        'customer_id' => $customer->id,
                        'name' => $adres['name'],
                        'sokak_cadde' => $adres['sokak_cadde'],
                        'bina_no' => $adres['bina_no'],
                        'kat' => $adres['kat'],
                        'daire_no' => $adres['daire_no'],
                        'mahalle' => $adres['mahalle'],
                        'adres_tarifi' => $adres['adres_tarifi'],
                        'latitude' => $location['lat'],
                        'longitude' => $location['lon'],
                    ]);
                }
            }
        }


        return redirect()->back()->with('message', 'Müşteri ve Adresleri Başarıyla Güncellendi.');
    }

    public function delete($id)
    {

        $customer = Customer::find($id);
        $customer->status = 'deactive';
        $customer->save();

        $customer_address = CustomerAddress::where('customer_id', $id)->get();
        if (count($customer_address) > 0) {
            foreach ($customer_address as $key => $value) {
                $value->status = 'deactive';
                $value->save();
            }
        }

        if ($customer) {
            echo "OK";
        } else {
            echo "ERR";
        }
    }
}
