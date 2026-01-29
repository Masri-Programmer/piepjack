<?php

use App\Http\Controllers\API\V1\AdminAuthController;
use App\Http\Controllers\API\V1\AdminUserController;
use App\Http\Controllers\API\V1\AdminDashboardController;
use App\Http\Controllers\API\V1\CategoryController;
use App\Http\Controllers\API\V1\ImageController;
use App\Http\Controllers\API\V1\OrderController;
use App\Http\Controllers\API\V1\ProductController;
use App\Http\Controllers\API\V1\ProductItemController;
use App\Http\Controllers\API\V1\ReturningController;
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
    Route::get('users', [AdminUserController::class, 'index']);
    Route::get('users/{user}', [AdminUserController::class, 'show']);
    Route::get('dashboard', [AdminDashboardController::class, 'stats']);
    Route::get('user', [AdminAuthController::class, 'show']);
    Route::post('ban/{user}', [AdminUserController::class, 'ban']);
    Route::post('logout', [AdminAuthController::class, 'logout']);
    Route::post('save', [ImageController::class, 'store']);
    Route::post('unban/{user}', [AdminUserController::class, 'unban']);
});
Route::prefix('admin')->group(
    function () {
        Route::get('/sanctum/csrf-cookie', function () {
            return response()->json(['message' => 'CSRF cookie set']);
        });
        Route::post('login', [AdminAuthController::class, 'login'])->middleware('guest');
    }
);
