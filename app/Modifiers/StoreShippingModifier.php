<?php

namespace App\Modifiers;

use Closure;
use Lunar\Base\ShippingModifier; // Lunar's base class
use Lunar\DataTypes\Price;
use Lunar\DataTypes\ShippingOption;
use Lunar\Facades\ShippingManifest;
use Lunar\Models\Contracts\Cart;
use Lunar\Models\TaxClass;

// Renamed to avoid PHP naming collisions
class StoreShippingModifier extends ShippingModifier
{
    public function handle(Cart $cart, Closure $next)
    {
        $taxClass = TaxClass::getDefault();

        // Safely get the subtotal (in cents)
        // We removed $cart->calculate() from here to prevent infinite loops!
        $subTotal = $cart->subTotal?->value ?? 0;

        // DHL Standard (Constant 5.90 EUR)
        $standardPrice = 590;

        ShippingManifest::addOption(
            new ShippingOption(
                name: 'DHL Standard',
                description: 'Standard shipping via DHL',
                identifier: 'DE_STD',
                price: new Price($standardPrice, $cart->currency, 1),
                taxClass: $taxClass
            )
        );

        // Rule 2: DHL Express (Always 9.90 EUR)
        ShippingManifest::addOption(
            new ShippingOption(
                name: 'DHL Express',
                description: 'Express shipping via DHL',
                identifier: 'DE_EXP',
                price: new Price(990, $cart->currency, 1),
                taxClass: $taxClass
            )
        );

        return $next($cart);
    }
}
