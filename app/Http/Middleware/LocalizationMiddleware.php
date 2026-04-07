<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class LocalizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->header('Accept-Language');

        if ($locale) {
            // Take the first 2 characters (e.g., 'en-US' -> 'en')
            $locale = substr($locale, 0, 2);

            if (in_array($locale, ['en', 'de'])) {
                App::setLocale($locale);
            }
        }

        return $next($request);
    }
}
