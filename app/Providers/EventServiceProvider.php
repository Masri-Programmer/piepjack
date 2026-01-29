<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\ProductReview;
use App\Observers\ProductReviewObserver;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        ProductReview::observe(ProductReviewObserver::class);
    }
}
