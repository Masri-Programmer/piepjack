<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AuthenticatedCustomer
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if (! Auth::guard('customer')->check()) {
            return response(['message' => 'Unauthenticated.'], 401);
        }

        return $next($request);
    }
}
