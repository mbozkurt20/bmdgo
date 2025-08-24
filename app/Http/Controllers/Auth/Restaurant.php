<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Restaurant extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('restaurant')->attempt($credentials)) {
            return redirect()->route('restaurant');
        }

        return redirect()->route('restaurant.login')->withErrors(['error' => 'Giriş bilgileri hatalı.']);
    }

    public function logout()
    {
        Auth::guard('restaurant')->logout(); // Superadmin oturumunu kapat
        return redirect()->route('restaurant.login'); // Login sayfasına yönlendir
    }
}
