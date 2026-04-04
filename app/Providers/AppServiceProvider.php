<?php

namespace App\Providers;

use App\Models\User;
use Filament\Panel;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Lunar\Admin\Support\Facades\LunarPanel;
use Stripe\Stripe;
use Lunar\Shipping\ShippingPlugin;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        LunarPanel::panel(function (Panel $panel) {
            return $panel->plugin(new ShippingPlugin());
        })->register();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        Gate::define('viewLogViewer', function (User $user) {
            return $user->hasRole('admin');
        });
    }
}
