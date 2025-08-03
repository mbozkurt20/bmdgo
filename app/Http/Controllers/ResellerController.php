<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ResellerController extends Controller
{
    use AuthenticatesUsers;

    protected function authenticated(Request $request, $user)
    {
        return redirect()->route('reseller.index');
    }
    public function logout(Request $request)
    {
        $this->guard()->logout();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
        ? new JsonResource([], 204)
            : redirect('/');
    }
    protected function loggedOut(Request $request)
    {
        return redirect()->route('reseller.login');
    }

    protected function guard()
    {
        return Auth::guard('reseller');
    }
}
