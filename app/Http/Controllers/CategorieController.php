<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CategorieController extends Controller
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
        $categories = Categorie::where('status','active')->where('restaurant_id', Auth::user()->id)->get();

        return view('restaurant.categories.index', compact('categories'));
    }

    /**
     * @returns
     */
    public function new()
    {
        return view('restaurant.categories.new');
    }

    public function edit($id)
    {
        $categorie = Categorie::find($id);

        return view('restaurant.categories.edit', compact('categorie'));
    }

    public function create(Request $request)
    {

        $data = $request->validate([
            'name' => 'required'
        ]);

        $create = New Categorie();
        $create->restaurant_id =  Auth::user()->id;
        $create->name = $data['name'];
        $create->desk = $request->desk;
        $create->save();

        return redirect()->back()->with('message', 'Kategori kaydı tamamlandı.');

    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => 'required'
        ]);

        $create = Categorie::find($request->id);
        $create->name = $data['name'];
        $create->desk = $request->desk;
        $create->save();

        return redirect()->back()->with('message', 'Kategori güncellendi.');

    }

    public function delete($id){

        $del = Categorie::find($id);
        $del->delete();
        if ($del) {
            echo "OK";
        } else {
            echo "ERR";
        }
    }
}
