<?php

namespace App\Modifiers;

use Closure;
use Lunar\DataTypes\Price;
use Lunar\Models\Contracts\Cart;

class StoreDiscountModifier
{
    public function handle(Cart $cart, Closure $next): Cart
    {

        return $next($cart);
    }
}
