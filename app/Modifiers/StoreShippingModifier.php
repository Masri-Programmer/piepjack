<?php

namespace App\Modifiers;

use App\Services\SendcloudService;
use Closure;
use Lunar\Base\ShippingModifier;
use Lunar\DataTypes\Price;
use Lunar\DataTypes\ShippingOption;
use Lunar\Facades\ShippingManifest;
use Lunar\Models\Contracts\Cart;
use Lunar\Models\TaxClass;

class StoreShippingModifier extends ShippingModifier
{
    public function __construct(protected SendcloudService $sendcloud) {}

    public function handle(Cart $cart, Closure $next)
    {
        $taxClass = TaxClass::getDefault();
        $shippingAddress = $cart->shippingAddress;
        $countryCode = $shippingAddress?->country?->iso2 ?? 'DE';

        $methods = $this->sendcloud->getShippingMethods();

        foreach ($methods as $method) {
            // Filter by country if countries are specified in the method
            if (! empty($method['countries'])) {
                $canShipTo = collect($method['countries'])->contains(fn ($c) => $c['iso_2'] === $countryCode);
                if (! $canShipTo) {
                    continue;
                }
            }

            // For now, we'll keep the existing flat rates for certain DHL methods if they match,
            // or use a default price if they don't.
            // In a real scenario, you might want to use Sendcloud's price or a markup.
            $price = 590; // Default flat rate
            if (str_contains(strtolower($method['name']), 'express')) {
                $price = 990;
            }

            ShippingManifest::addOption(
                new ShippingOption(
                    name: $method['name'],
                    description: $method['service']['name'] ?? 'Shipping via '.$method['carrier'],
                    identifier: (string) $method['id'],
                    price: new Price($price, $cart->currency, 1),
                    taxClass: $taxClass
                )
            );
        }

        return $next($cart);
    }
}
