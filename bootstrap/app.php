<?php

use Illuminate\Http\Request;
use App\Http\Middleware\Admin;
use Illuminate\Foundation\Application;
use App\Http\Middleware\AuthenticatedUser;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // $middleware->statefulApi(); CSRF
        $middleware->alias([
            'admin' => Admin::class,
            'user' => AuthenticatedUser::class,
            'doNotCacheResponse' => \Spatie\ResponseCache\Middlewares\DoNotCacheResponse::class,
    ]);
        $middleware->web(append: [
            \Spatie\ResponseCache\Middlewares\CacheResponse::class,
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
