<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Courier;
use App\Models\Expenses;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RestaurantsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $restaurants = Restaurant::where('status', 'active')->where('admin_id', auth()->id())->get();

        return view('admin.restaurants.index', compact('restaurants'));
    }
    public function new()
    {
        return view('admin.restaurants.new');
    }
    public function edit($id)
    {
        $restaurant = Restaurant::find($id);

        return view('admin.restaurants.edit', compact('restaurant'));
    }

    public function create(Request $request)
    {
        $testMode = env('TEST_MODE');

        if ($testMode) {
            if (Restaurant::count() > env('TEST_MODE_LIMIT')) {
                return redirect()->back()->with('test', 'Test Modu: Üzgünüz, En Fazla '.env('TEST_MODE_LIMIT').' Kayıt Ekleyebilirsiniz');
            }
        }

        if (Restaurant::where('restaurant_name',$request->restaurant_name)->exists()) {
            return redirect()->back()->with('test', 'Bu isimde bir restaurant zaten mevcut!!');
        }

        $create = new Restaurant();
        $create->admin_id = auth()->id();
        $create->restaurant_code = "RES-" . rand(9, 99999);
        $create->restaurant_name = $request->restaurant_name;
        $create->name = $request->name;
        $create->email = $request->email;
        $create->phone = $request->phone;
        $create->password = Hash::make($request->password);
        $create->tax_name = $request->tax_name;
        $create->tax_number = $request->tax_number;
        $create->entegra_id = $request->entegra_id;
        $create->entegra_token = $request->entegra_token;
        $create->package_price = $request->package_price;
        $create->address = $request->address;
        $create->save();

        return redirect()->back()->with('message', 'Restaurant Kaydı Tamamlandı.');
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'restaurant_name' => 'required',
            'email' => 'required',
            'phone' => 'required'
        ]);

        $create = Restaurant::find($request->id);

        $create->restaurant_name = $data['restaurant_name'];
        $create->name = $data['name'];
        $create->email = $data['email'];
        $create->phone = $data['phone'];
        if (isset($data->password)) {
            $create->password = Hash::make($data['password']);
        }
        $create->tax_name = $request->tax_name;
        $create->tax_number = $request->tax_number;
        $create->address = $request->address;
        $create->entegra_id = $request->entegra_id;
        $create->entegra_token = $request->entegra_token;
        $create->package_price = $request->package_price;
        $create->status = $request->status;

        $create->save();

        return redirect()->back()->with('message', 'İşyeri bilgileri güncellendi.');
    }

    public function delete($id)
    {
        $del = Restaurant::find($id);
        $del->status = 'deactive';
        $sav = $del->save();

        if ($sav) {
            echo "OK";
        } else {
            echo "ERR";
        }
    }
}
