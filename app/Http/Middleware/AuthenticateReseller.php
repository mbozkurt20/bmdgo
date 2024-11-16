<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class AuthenticateReseller extends Middleware
{
    protected function authenticate($request, array $guards)
    {
        if ($this->auth->guard('reseller')->check()) {
            return $this->auth->shouldUse('reseller');
        }
        $this->unauthenticated($request, ['reseller']);
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
            return route('reseller.login');
        }
    }
}
