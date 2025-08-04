<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use App\Models\Customer;
use App\Models\CustomerAddress;
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

    public function edit($id)
    {
        $customer = Customer::find($id);
        return view('restaurant.customers.edit', compact('customer'));
    }

    public function create(Request $request)
    {
        if (env('TEST_MODE') && Customer::count() == 0) {
            // Save customer information
            $create = new Customer();
            $create->restaurant_id = Auth::user()->id; // Assuming the authenticated user is the restaurant
            $create->name = $request->input('name');
            $create->phone = $request->input('phone');
            $create->mobile = $request->input('mobile');
            $create->email = $request->input('email')??null;
            $create->save();

            // Check if address data is present
            if ($request->address) {
                foreach ($request->address as $adres) {
                    // Save each address for the customer
                    $address = new CustomerAddress();
                    $address->customer_id = $create->id; // Associate address with the created customer
                    $address->restaurant_id = Auth::user()->id; // Associate address with the restaurant
                    $address->name = $adres['name']; // Address title
                    $address->sokak_cadde = $adres['sokak_cadde'];
                    $address->bina_no = $adres['bina_no'];
                    $address->kat = $adres['kat'];
                    $address->daire_no = $adres['daire_no'];
                    $address->mahalle = $adres['mahalle'];
                    $address->adres_tarifi = $adres['adres_tarifi'] ?? ''; // Set empty string if not provided
                    $address->save();
                }
            }

            return redirect()->back()->with('message', 'Müşteri Başarıyla Eklendi.');
        } else {
            return redirect()->back()->with('test', 'Test Modu: Üzgünüz, En Fazla 1 Kayıt Ekleyebilirsiniz');
        }
    }

    public function update(Request $request)
    {
        $customer = Customer::find($request->id);
        $customer->name = $request->name;
        $customer->phone = $request->phone;
        $customer->mobile = $request->mobile;
        $customer->save();

        if ($request->address) {
            foreach ($request->address as $adres) {
                if ($adres['type'] == "up") {
                    $address = CustomerAddress::where('id', $adres['id'])->first();
                    if ($address) {
                        $address->name = $adres['name'];
                        $address->sokak_cadde = $adres['sokak_cadde'];
                        $address->bina_no = $adres['bina_no'];
                        $address->kat = $adres['kat'];
                        $address->daire_no = $adres['daire_no'];
                        $address->mahalle = $adres['mahalle'];
                        $address->adres_tarifi = $adres['adres_tarifi'];
                        $address->save();
                    }
                } else {
                    $newAddress = new CustomerAddress();
                    $newAddress->restaurant_id = Auth::user()->id;
                    $newAddress->customer_id = $customer->id;
                    $newAddress->name = $adres['name'];
                    $newAddress->sokak_cadde = $adres['sokak_cadde'];
                    $newAddress->bina_no = $adres['bina_no'];
                    $newAddress->kat = $adres['kat'];
                    $newAddress->daire_no = $adres['daire_no'];
                    $newAddress->mahalle = $adres['mahalle'];
                    $newAddress->adres_tarifi = $adres['adres_tarifi'];
                    $newAddress->save();
                }
            }
        }

        return redirect()->back()->with('message', 'Müşteri Kaydı Başarıyla Güncellendi.');
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
