<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class RouteRedirectCheckout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if ( ! $request->ajax() && !Auth::check() && $request->routeIs('checkout'))
        {

            Cookie::queue('redirect_after_auth_checkout', url()->current(), 10);

        }

        return $next($request);
    }
}
