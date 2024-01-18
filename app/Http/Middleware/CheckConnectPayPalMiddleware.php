<?php

namespace App\Http\Middleware;

use App\Facades\PayPalProcessor;
use App\Models\User;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Laracasts\Flash\Flash;

class CheckConnectPayPalMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (PayPalProcessor::checkConnect()) {
            if (Auth::check() && Auth::user()->hasRole(User::ROLE_INSTRUCTOR)) {
                // перевірити статус інструктора
                PayPalProcessor::checkMerchantStatus(Auth::user());
            }
            return $next($request);
        } else {
            Flash::error('Connection to PayPal service is not possible')->important();
            return $next($request);
        }

    }
}
