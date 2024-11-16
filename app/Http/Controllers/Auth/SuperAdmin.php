<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SuperAdmin extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::guard('superadmin')->attempt($credentials)) {
            return redirect()->route('superadmin.dashboards');
        }

        return redirect()->route('superadmin.login')->withErrors(['error' => 'Giriş bilgileri hatalı.']);
    }


    public function logout()
    {
        Auth::guard('superadmin')->logout(); // Superadmin oturumunu kapat
        return redirect()->route('superadmin.login'); // Login sayfasına yönlendir
    }
}
