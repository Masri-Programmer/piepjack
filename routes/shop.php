<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\API\V1\CheckoutController;
use App\Http\Controllers\API\V1\VariationController;
use App\Http\Controllers\API\V1\PublicProductController;
use App\Http\Controllers\API\V1\ProductReviewController;
use App\Http\Controllers\API\V1\PublicCategoryController;
use App\Http\Controllers\API\V1\PublicReturningController;
use App\Http\Controllers\API\V1\PublicOrderController;

Route::prefix('shop')->as('shop.')->group(function () {
    Route::apiResource('products', PublicProductController::class)->only(['show', 'index']);
    Route::apiResource('returns', PublicReturningController::class)->only(['show', 'store']);
    Route::apiResource('orders', PublicOrderController::class)->only(['show', 'index']);
    Route::get('products-reviews/{product}', [ProductReviewController::class, 'index']);
    Route::post('products-reviews', [ProductReviewController::class, 'store']);
    Route::get('categories', [PublicCategoryController::class, 'index']);
    Route::get('category-variations/{category}', [VariationController::class, 'categoryVariations']);
    Route::post('checkout', [CheckoutController::class, 'checkout']);
    Route::get('sendTestEmail/{orderId}', [CheckoutController::class, 'sendTestEmail']);
    Route::get('sendReturnTestEmail/{returnId}', [PublicReturningController::class, 'sendReturnEmailTest']);
    Route::post('webhook/stripe', [CheckoutController::class, 'handleWebhook']);
    Route::post('webhook/return-items', [PublicReturningController::class, 'handleWebhook']);
    Route::post('update-order-status', [CheckoutController::class, 'updateOrderStatus']);
    Route::middleware('auth:sanctum')->group(function () { });
});
