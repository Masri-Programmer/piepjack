<?php

namespace App\Modifiers;

use Closure;
use Lunar\DataTypes\Price;
use Lunar\Models\Contracts\Cart;

class StoreDiscountModifier
{
    public function handle(Cart $cart, Closure $next): Cart
    {
        $discountTotal = 0;

        // Use subTotal (camelCase) for Lunar 1.x Price object
        $subTotal = $cart->subTotal?->value ?? 0; // in cents

        // 1. 5% Discount for > 100 EUR
        if ($subTotal > 10000) {
            $discountTotal += (int) ($subTotal * 0.05);
        }

        // 2. Promo Code (Reading from cart meta)
        $promoCode = $cart->meta['promo_code'] ?? null;
        if ($promoCode && strtolower(trim($promoCode)) === 'pickup') {
            $discountTotal += 1000; // 10 EUR in cents
        }

        // Apply to the cart's discountTotal property
        if ($discountTotal > 0) {
            $cart->discountTotal = new Price($discountTotal, $cart->currency, 1);
        }

        return $next($cart);
    }
}