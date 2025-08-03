<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        if(Auth::guard('restaurant')->check()){
            return redirect()->route('restaurant.index');
        }else{
            return view('restaurant.login');
        }
    }
}
