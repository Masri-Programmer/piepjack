<?php

use App\Http\Controllers\API\V1\AdminAuthController;
use App\Http\Controllers\API\V1\AdminCustomerController;
use App\Http\Controllers\API\V1\AdminDashboardController;
use App\Http\Controllers\API\V1\CategoryController;
use App\Http\Controllers\API\V1\ImageController;
use App\Http\Controllers\API\V1\OrderController;
use App\Http\Controllers\API\V1\ProductController;
use App\Http\Controllers\API\V1\ProductItemController;
use App\Http\Controllers\API\V1\ReturningController;
use App\Http\Controllers\API\V1\ShutdownController;
use App\Http\Controllers\API\V1\VariationController;
use App\Http\Controllers\API\V1\VariationOptionController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->middleware('auth:sanctum')->as('admin.')->group(function () {
    Route::apiResource('product-items', ProductItemController::class);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('orders', OrderController::class);
    Route::apiResource('returns', ReturningController::class);
    Route::apiResource('variation-options', VariationOptionController::class);
    Route::apiResource('variations', VariationController::class);
    Route::get('all-categories', [CategoryController::class, 'all']);
    Route::get('category-variations/{category}', [VariationController::class, 'categoryVariations']);
    Route::get('customers', [AdminCustomerController::class, 'index']);
    Route::get('customers/{customer}', [AdminCustomerController::class, 'show']);
    Route::get('dashboard', [AdminDashboardController::class, 'stats']);
    Route::get('user', [AdminAuthController::class, 'show'])->middleware('auth:sanctum');
    Route::post('ban/{customer}', [AdminCustomerController::class, 'ban']);
    Route::post('logout', [AdminAuthController::class, 'logout']);
    Route::post('save', [ImageController::class, 'store']);
    Route::post('unban/{customer}', [AdminCustomerController::class, 'unban']);
});
Route::prefix('admin')->group(
    function () {
        Route::get('/sanctum/csrf-cookie', function () {
            return response()->json(['message' => 'CSRF cookie set']);
        });
        Route::get('shutdown-code', [ShutdownController::class, 'getShutdownCode'])->middleware('guest');
        Route::post('login', [AdminAuthController::class, 'login'])->middleware('guest');
    }
);