<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use App\Models\Restaurant;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProductController extends Controller
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
        $products = Product::where('status','active')->where('restaurant_id', Auth::user()->id)->get();

        return view('restaurant.products.index', compact('products'));
    }

    /**
     * @returns
     */
    public function new()
    {
        $categories = Categorie::where('status', 'active')->where('restaurant_id', Auth::user()->id)->get();
        return view('restaurant.products.new', compact('categories'));
    }

    public function edit($id)
    {
        $product = Product::find($id);
        $categories = Categorie::where('status', 'active')->get();

        return view('restaurant.products.edit', compact('product','categories'));
    }

    public function create(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'price' => 'required',
        ]);

        $create = New Product();

        if ($request->hasFile('image')) { /* resim geldi mi */
            $newsImage = $request->image;
            $newsImageName = date("YmdHis").'-'.rand(9,9999).'.'.$newsImage->getClientOriginalExtension();
            $request->image->move(public_path('/upload/products'), $newsImageName);
            $pageimages = "/upload/products/" . $newsImageName;

            $create->image = $pageimages;
        }

        $create->restaurant_id =  Auth::user()->id;
        $create->name = $data['name'];
        $create->categorie_id = $request->categorie_id;
        $create->code = $request->code;
        $create->price = $data['price'];
        $create->preparation_time = $request->preparation_time;
        $create->details = $request->details;
        $create->begenilen = $request->begenilen;
        $create->save();

        return redirect()->back()->with('message', 'Ürün kaydı tamamlandı.');

    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'price' => 'required',
        ]);

        $create = Product::find($request->id);

        if ($request->hasFile('image')) { /* resim geldi mi */
            $newsImage = $request->image;
            $newsImageName = date("YmdHis").'-'.rand(9,9999).'.'.$newsImage->getClientOriginalExtension();
            $request->image->move(public_path('/upload/products'), $newsImageName);
            $pageimages = "/upload/products/" . $newsImageName;

            $create->image = $pageimages;
        }

        $create->name = $data['name'];
        $create->code = $request->code;
        $create->categorie_id = $request->categorie_id;
        $create->price = $data['price'];
        $create->preparation_time = $request->preparation_time;
        $create->details = $request->details;
        $create->begenilen = $request->begenilen;
        $create->save();

        return redirect()->back()->with('message', 'Ürün kaydı güncellendi.');

    }

    public function delete($id){

        $del = Product::find($id);
        $del->delete();
        if ($del) {
            echo "OK";
        } else {
            echo "ERR";
        }
    }

}
