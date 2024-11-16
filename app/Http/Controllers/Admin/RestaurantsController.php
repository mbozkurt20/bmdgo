<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RestaurantsController extends Controller
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
        $restaurants = Restaurant::where('status','active')->where('admin_id', auth()->id())->get();

        return view('admin.restaurants.index', compact('restaurants'));
    }

    /**
     * @returns
     */
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
        $data = $request->validate([
            'name' => 'required',
            'restaurant_name' => 'required',
            'email' => 'required|unique:restaurants',
            'phone' => 'required',
            'password' => 'required',
        ]);

        $create = New Restaurant();
        $create->restaurant_code = "RES-".rand(9,99999);
        $create->restaurant_name = $data['restaurant_name'];
        $create->name = $data['name'];
        $create->email = $data['email'];
        $create->phone = $data['phone'];
        $create->password = Hash::make($data['password']);
        $create->tax_name = $request->tax_name;
        $create->tax_number = $request->tax_number;
        $create->entegra_id = $request->entegra_id;
        $create->entegra_token = $request->entegra_token;
        $create->package_price = $request->package_price;
        $create->address = $request->address;
        $create->save();

        return redirect()->back()->with('message', 'İşyeri kaydı tamamlandı.');

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
        if(isset($data->password)){
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


    public function delete($id){

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