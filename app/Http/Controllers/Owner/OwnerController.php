<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OwnerController extends Controller
{
    public function showLoginForm()
    {
        return view('owner.auth.login');
    }

    // Login işlemi
    public function login(Request $request)
    {
        // Basit giriş doğrulaması
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('owner')->attempt($credentials)) {
            return redirect()->route('dashboard');
        }

        return back()->withErrors(['email' => 'Giriş başarısız oldu.']);
    }

    // Dashboard
    public function dashboard()
    {
        return view('dashboard');
    }

    // Kontör yükleme formu
    public function showTopUpForm()
    {
        return view('admin.topup');
    }

    // Kontör yükleme işlemi
    public function topUp(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        // Burada kontör güncelleme işlemi yapılır
        // Örneğin, şu anki kullanıcının kontörünü güncelle
        // $user = auth()->user();
        // $user->top_up($request->amount);

        return back()->with('success', 'Kontör başarıyla yüklendi.');
    }

    // Dealer (kullanıcı) kaydı göster
    public function showDealerForm()
    {
        return view('dealer.create');
    }

    // Dealer kaydet
    public function storeDealer(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'lat' => 'required|numeric',
            'long' => 'required|numeric',
            'city' => 'required|string|max:100',
            'district' => 'required|string|max:100',
        ]);

        // Kullanıcı kaydı işlemi (örneğin, User modeli)
        // User::create([...])

        return redirect()->route('dashboard')->with('success', 'Dealer kaydı başarıyla yapıldı.');
    }
}
