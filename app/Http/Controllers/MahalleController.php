<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mahalle;

class MahalleController extends Controller
{
    public function create()
    {
        $mahalleler = Mahalle::where('ilce_id', 538)->pluck('mahalle_adi', 'id');

        // Veriyi debug etmek için
        dd($mahalleler);

        // Veriyi view'a gönder
        return view('restaurant.orders.new', ['mahalleler' => $mahalleler]);
    }
}