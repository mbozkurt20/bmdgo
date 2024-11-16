<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $customers = Customer::where('status', 'active')->where('restaurant_id', Auth::user()->id)->get();

        return view('restaurant.customers.index', compact('customers'));
    }

    /**
     * @returns
     */
    public function new()
    {
        $iller = DB::table('iller')->get();
        return view('restaurant.customers.new', compact('iller'));
    }

    public function edit($id)
    {
        $customer = Customer::find($id);

        return view('restaurant.customers.edit', compact('customer'));
    }

    public function create(Request $request)
    {
        // Validate customer data
        $data = $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'mobile' => 'required'
        ]);

        // Save customer information
        $create = new Customer();
        $create->restaurant_id = Auth::user()->id; // Assuming the authenticated user is the restaurant
        $create->name = $data['name'];
        $create->phone = $data['phone'];
        $create->mobile = $data['mobile'];
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

        return redirect()->back()->with('message', 'Müşteri kaydı tamamlandı.');
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'phone' => 'required',
        ]);

        $create = Customer::find($request->id);
        $create->name = $request->name;
        $create->phone = $request->phone;
        $create->mobile = $request->mobile;
        $create->save();

        if ($request->address) {
            foreach ($request->address as $adres) {

                if ($adres->type == "up") {

                    $adreses = CustomerAddress::where('id', $adres->id)->first();
                    if ($adreses) {

                        $adreses->name = $adres['name'];
                        $adreses->sokak_cadde = $adres['sokak_cadde'];
                        $adreses->bina_no = $adres['bina_no'];
                        $adreses->kat = $adres['kat'];
                        $adreses->daire_no = $adres['daire_no'];
                        $adreses->mahalle = $adres['mahalle'];
                        $adreses->adres_tarifi = $adres['adres_tarifi'];
                        $adreses->save();
                    }
                } else {
                    $adreses = new CustomerAddress();
                    $adreses->restaurant_id =  Auth::user()->id;
                    $adreses->customer_id = $adres->customer_id;
                    $adreses->name = $adres['name'];
                    $adreses->sokak_cadde = $adres['sokak_cadde'];
                    $adreses->bina_no = $adres['bina_no'];
                    $adreses->kat = $adres['kat'];
                    $adreses->daire_no = $adres['daire_no'];
                    $adreses->mahalle = $adres['mahalle'];
                    $adreses->adres_tarifi = $adres['adres_tarifi'];
                    $adreses->save();
                }
                $adreses->restaurant_id =  Auth::user()->id;
            }
        }

        return redirect()->back()->with('message', 'Müşteri kaydı güncellendi.');
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
