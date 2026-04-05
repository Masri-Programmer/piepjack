<?php

namespace App\Providers;

use App\Models\User;
use Filament\Panel;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Lunar\Admin\Support\Facades\LunarPanel;
use Lunar\Shipping\ShippingPlugin;
use Stripe\Stripe;
use Faker\Generator as FakerGenerator;
use Faker\Factory as FakerFactory;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        LunarPanel::panel(function (Panel $panel) {
    //         $this->app->singleton(FakerGenerator::class, function () {
    //     // This creates a generator that tries German first, then falls back to English
    //     return FakerFactory::create('de_DE'); 
    // });
            return $panel->plugin(new ShippingPlugin)->brandLogo(fn () => asset('img/logo-new.png'))
                ->brandLogoHeight('2.5rem')
                ->darkModeBrandLogo(fn () => asset('img/logo-new.png'))
                ->favicon(asset('img/favicon/favicon-32x32.png'));

        })->register();
    }

    /**->brandLogo(fn() => asset('img/logo-new.png'))
                ->brandLogoHeight('2.5rem')
                ->darkModeBrandLogo(fn() => asset('img/logo-new.png'));
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
