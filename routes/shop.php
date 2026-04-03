<?php

use App\Http\Controllers\API\V1\CheckoutController;
use App\Http\Controllers\API\V1\ProductReviewController;
use App\Http\Controllers\API\V1\PublicCategoryController;
use App\Http\Controllers\API\V1\PublicOrderController;
use App\Http\Controllers\API\V1\PublicProductController;
use App\Http\Controllers\API\V1\PublicReturningController;
use App\Http\Controllers\API\V1\VariationController;
use Illuminate\Support\Facades\Route;

Route::prefix('shop')->as('shop.')->group(function () {
    Route::apiResource('products', PublicProductController::class)->only(['show', 'index']);
    Route::apiResource('returns', PublicReturningController::class)->only(['show', 'store']);
    Route::apiResource('orders', PublicOrderController::class)->only(['show', 'index']);
    Route::get('products-reviews/{product}', [ProductReviewController::class, 'index']);
    Route::post('products-reviews', [ProductReviewController::class, 'store'])->middleware('throttle:3,1');
    Route::get('categories', [PublicCategoryController::class, 'index']);
    Route::get('category-variations/{category}', [VariationController::class, 'categoryVariations']);
    Route::post('checkout', [CheckoutController::class, 'checkout'])->middleware('throttle:5,1');
    // Route::get('sendTestEmail/{orderId}', [CheckoutController::class, 'sendTestEmail']);
    Route::get('sendReturnTestEmail/{returnId}', [PublicReturningController::class, 'sendReturnEmailTest']);
    Route::post('webhook/stripe', [CheckoutController::class, 'handleWebhook'])->middleware('throttle:60,1');
    Route::post('webhook/return-items', [PublicReturningController::class, 'handleWebhook']);
    Route::post('launch-registration', [\App\Http\Controllers\API\V1\LaunchRegistrationController::class, 'store'])->middleware('throttle:3,1');
    Route::post('trigger-online-notification', [\App\Http\Controllers\API\V1\LaunchRegistrationController::class, 'triggerOnlineNotification']);
    Route::middleware('auth:sanctum')->group(function () {});
});
