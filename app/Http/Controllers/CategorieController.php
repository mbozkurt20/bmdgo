<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use App\Models\Courier;
use App\Models\Expenses;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CategorieController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $categories = Categorie::where('status','active')->where('restaurant_id', Auth::user()->id)->get();

        return view('restaurant.categories.index', compact('categories'));
    }

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
        $testMode = env('TEST_MODE');

        if ($testMode) {
            if (Categorie::count() > env('TEST_MODE_LIMIT')) {
                return redirect()->back()->with('test', 'Test Modu: Üzgünüz, En Fazla '.env('TEST_MODE_LIMIT').' Kayıt Ekleyebilirsiniz');
            }
        }

        $data = $request->validate([
            'name' => 'required',
        ]);

        $lastCategory = Categorie::where('restaurant_id', Auth::user()->id)->orderByDesc('id')->first();
        $desk = $data['desk'] ?? $lastCategory ? $lastCategory->id+1 : 1; ;
        $create = new Categorie();
        $create->restaurant_id = Auth::user()->id;
        $create->name = $data['name'];
        $create->desk = $desk;
        $create->save();

        return redirect()->back()->with('message', 'Kategori Başarıyla Eklendi.');
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
