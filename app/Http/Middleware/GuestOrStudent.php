<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use App\Models\User;

class GuestOrStudent
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
        if ( Auth::user() && !Auth::user()->hasRole(User::ROLE_STUDENT)) {
            return abort(403);
        }

        return $next($request);
    }
}
