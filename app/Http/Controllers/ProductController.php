<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use App\Models\Courier;
use App\Models\Restaurant;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $products = Product::where('status', 'active')->where('restaurant_id', Auth::user()->id)->get();

        return view('restaurant.products.index', compact('products'));
    }

    public function new()
    {
        $categories = Categorie::where('status', 'active')->where('restaurant_id', Auth::user()->id)->get();
        return view('restaurant.products.new', compact('categories'));
    }

    public function edit($id)
    {
        $product = Product::find($id);
        $categories = Categorie::where('status', 'active')->get();

        return view('restaurant.products.edit', compact('product', 'categories'));
    }

    public function create(Request $request)
    {
        if (env('TEST_MODE') && Product::count() == 0){
            $data = $request->validate([
                'name' => 'required',
                'price' => 'required',
            ]);

            $create = new Product();

            if ($request->hasFile('image')) { /* resim geldi mi */
                $newsImage = $request->image;
                $newsImageName = date("YmdHis") . '-' . rand(9, 9999) . '.' . $newsImage->getClientOriginalExtension();
                $request->image->move(public_path('/upload/products'), $newsImageName);
                $pageimages = "/upload/products/" . $newsImageName;

                $create->image = $pageimages;
            }

            $create->restaurant_id = Auth::user()->id;
            $create->name = $data['name'];
            $create->category_id = $request->category_id;
            $create->code = $request->code;
            $create->price = $data['price'];
            $create->preparation_time = $request->preparation_time;
            $create->details = $request->details;
            $create->begenilen = $request->begenilen;
            $create->save();

            return redirect()->back()->with('message', 'Ürün Başarıyla Eklendi');
        }else{
            return redirect()->back()->with('test', 'Test Modu: Üzgünüz, En Fazla 1 Kayıt Ekleyebilirsiniz');
        }
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
            $newsImageName = date("YmdHis") . '-' . rand(9, 9999) . '.' . $newsImage->getClientOriginalExtension();
            $request->image->move(public_path('/upload/products'), $newsImageName);
            $pageimages = "/upload/products/" . $newsImageName;

            $create->image = $pageimages;
        }

        $create->name = $data['name'];
        $create->code = $request->code;
        $create->category_id = $request->category_id;
        $create->price = $data['price'];
        $create->preparation_time = $request->preparation_time;
        $create->details = $request->details;
        $create->begenilen = $request->begenilen;
        $create->save();

        return redirect()->back()->with('message', 'Ürün Başarıyla Güncellendi.');
    }

    public function delete($id)
    {
        $del = Product::find($id);
        $del->delete();
        if ($del) {
            echo "OK";
        } else {
            echo "ERR";
        }
    }
}
