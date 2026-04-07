<?php

namespace App\Providers;

use App\Filament\Resources\ReturningResource;
use App\Models\User;
use App\Modifiers\StoreShippingModifier;
use Faker\Factory as FakerFactory;
use Faker\Generator as FakerGenerator;
use Filament\Panel;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Lunar\Admin\Support\Facades\LunarPanel;
use Lunar\Base\ShippingModifiers;
use Lunar\Shipping\ShippingPlugin;
use Stripe\Stripe;

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
                ->favicon(asset('img/favicon/favicon-32x32.png'))
                ->resources([
                    ReturningResource::class,
                ]);

        })->register();
    }

    public function boot(ShippingModifiers $shippingModifiers): void
    {
        $shippingModifiers->add(StoreShippingModifier::class);
        Stripe::setApiKey(config('services.stripe.secret'));

        Gate::define('viewLogViewer', function (User $user) {
            return $user->hasRole('admin');
        });
    }
}
