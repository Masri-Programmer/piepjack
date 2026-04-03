<?php

use App\Http\Middleware\Admin;
use App\Http\Middleware\AuthenticatedUser;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Spatie\ResponseCache\Middlewares\DoNotCacheResponse;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->prefix('api/V1')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));

            Route::middleware('web')
                ->prefix('api/V1')
                ->group(base_path('routes/shop.php'));

            Route::middleware('api')
                ->group(base_path('routes/ai.php'));

            // This catch-all route must be at the very bottom of all routes
            Route::middleware('web')->get('/{any?}', function () {
                return view('app');
            })->where('any', '.*');
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->statefulApi();
        $middleware->alias([
            'admin' => Admin::class,
            'user' => AuthenticatedUser::class,
            'doNotCacheResponse' => DoNotCacheResponse::class,
        ]);
        $middleware->web(append: [
            // \Spatie\ResponseCache\Middlewares\CacheResponse::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'shop/webhook/*',
            'shop/webhooks/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            if ($request->is('api/*')) {
                return true;
            }

            return $request->expectsJson();
        });
    })

    ->create();
