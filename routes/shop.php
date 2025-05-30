<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\API\V1\CheckoutController;
use App\Http\Controllers\API\V1\ShutdownController;
use App\Http\Controllers\API\V1\VariationController;
use App\Http\Controllers\API\V1\PublicProductController;
use App\Http\Controllers\API\V1\PublicCategoryController;
use App\Http\Controllers\API\V1\PublicReturningController;
use App\Http\Controllers\API\V1\PublicOrderController;

Route::prefix('shop')->as('shop.')->group(function () {
    Route::apiResource('products', PublicProductController::class)->only(['show', 'index']);
    Route::apiResource('returns', PublicReturningController::class)->only(['show', 'store']);
    Route::apiResource('orders', PublicOrderController::class)->only(['show', 'index']);
    Route::get('categories', [PublicCategoryController::class, 'index']);
    Route::get('category-variations/{category}', [VariationController::class, 'categoryVariations']);
    Route::get('generate-shutdown-code', [ShutdownController::class, 'generateShutdownCode']);
    Route::get('shutdown-code', [ShutdownController::class, 'getShutdownCode']);
    Route::post('checkout', [CheckoutController::class, 'checkout']);
    // Route::get('sendTestEmail', [CheckoutController::class, 'sendTestEmail']);
    // Route::get('sendReturnEmailTest', [PublicReturningController::class, 'sendReturnEmailTest']);
    Route::post('webhook/stripe', [CheckoutController::class, 'handleWebhook']);
    Route::post('webhook/return-items', [PublicReturningController::class, 'handleWebhook']);
    Route::post('shutdown', [ShutdownController::class, 'shutdown']);
    Route::post('update-order-status', [CheckoutController::class, 'updateOrderStatus']);
    Route::middleware('auth:sanctum')->group(function () {});



    Route::get('schedule-list', function () {
        $output = Artisan::call('schedule:list');

        $outputContent = Artisan::output();

        return response($outputContent, 200)
            ->header('Content-Type', 'text/plain');
    });

    Route::get('clear-cache', function () {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            return response()->json(['message' => 'Application cache cleared successfully! Config, route, and view caches also cleared.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error clearing cache: ' . $e->getMessage()], 500);
        }
    });

    Route::get('run-migrations', function () {
        try {
            Artisan::call('migrate', ['--force' => true]);
            return response()->json(['message' => 'Migrations run successfully!', 'output' => Artisan::output()]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error running migrations: ' . $e->getMessage()], 500);
        }
    });
});
