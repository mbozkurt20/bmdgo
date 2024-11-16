<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class AuthenticateMerchant extends Middleware
{
    protected function authenticate($request, array $guards)
    {
        if ($this->auth->guard('restaurant')->check()) {
            return $this->auth->shouldUse('restaurant');
        }
        $this->unauthenticated($request, ['restaurant']);
    }
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('restaurant.login');
        }
    }
}
