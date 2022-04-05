<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;
use Log;

class RememberHttpReferer
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
        if (!Auth::user()) {
//            dd($request->server('HTTP_REFERER'));
			if ( session()->has('current_admin_page') ){ // redirect after login
//				Log::info('set prev page = '. session()->get('current_admin_page'));
				session()->put('prev_admin_page', session()->get('current_admin_page'));
			}
			session()->put('current_admin_page', $request->fullUrl());
//			Log::info('set current page = '. $request->fullUrl());
        }else{
			session()->forget('current_admin_page');
			session()->forget('prev_admin_page');
		}

        return $next($request);
    }
}
