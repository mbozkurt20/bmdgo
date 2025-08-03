<?php

namespace App\Http\Controllers;

use App\Models\Quarter;
use Illuminate\Http\Request;
use App\Models\Mahalle;

class MahalleController extends Controller
{
    public function create()
    {
        $mahalleler = Quarter::where('district_id', 538)->pluck('name', 'id');

        // Veriyi debug etmek için
        dd($mahalleler);

        // Veriyi view'a gönder
        return view('restaurant.orders.new', ['mahalleler' => $mahalleler]);
    }
}
