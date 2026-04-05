<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Lunar\Models\Country;
use Lunar\Models\Currency;
use Lunar\Models\Price;
use Lunar\Shipping\Models\ShippingMethod;
use Lunar\Shipping\Models\ShippingRate;
use Lunar\Shipping\Models\ShippingZone;

class ShippingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currency = Currency::getDefault();

        $standardShipping = ShippingMethod::create([
            'name' => 'Standard Shipping',
            'code' => 'STNDRD',
            'enabled' => true,
            'driver' => 'ship-by',
            'data' => [
                'charge_by' => 'cart_total',
            ],
        ]);

        $ukShippingZone = ShippingZone::create([
            'name' => 'UK',
            'type' => 'countries',
        ]);

        $ukShippingRate = ShippingRate::create([
            'shipping_zone_id' => $ukShippingZone->id,
            'shipping_method_id' => $standardShipping->id,
            'enabled' => true,
        ]);

        $ukShippingZone->countries()->sync(
            Country::where('iso3', '=', 'GBR')->first()->id,
        );

        Price::create([
            'priceable_type' => (new ShippingRate)->getMorphClass(),
            'priceable_id' => $ukShippingRate->id,
            'price' => 1000,
            'min_quantity' => 1,
            'currency_id' => $currency->id,
        ]);

        // Free shipping on £100 or over orders
        Price::create([
            'priceable_type' => (new ShippingRate)->getMorphClass(),
            'priceable_id' => $ukShippingRate->id,
            'price' => 0,
            'min_quantity' => 10000,
            'currency_id' => $currency->id,
        ]);

        // US Shipping

        $usShipping = ShippingMethod::create([
            'name' => 'US Shipping',
            'code' => 'USA',
            'enabled' => true,
            'driver' => 'ship-by',
            'data' => [
                'charge_by' => 'cart_total',
            ],
        ]);

        $usShippingZone = ShippingZone::create([
            'name' => 'America',
            'type' => 'countries',
        ]);

        $usShippingRate = ShippingRate::create([
            'shipping_zone_id' => $usShippingZone->id,
            'shipping_method_id' => $usShipping->id,
            'enabled' => true,
        ]);

        $usShippingZone->countries()->sync(
            Country::where('iso3', '=', 'USA')->first()->id,
        );

        Price::create([
            'priceable_type' => (new ShippingRate)->getMorphClass(),
            'priceable_id' => $usShippingRate->id,
            'price' => 5000,
            'min_quantity' => 1,
            'currency_id' => $currency->id,
        ]);

        // European shipping

        $euroShipping = ShippingMethod::create([
            'name' => 'Europe Delivery',
            'code' => 'EURO',
            'enabled' => true,
            'driver' => 'ship-by',
        ]);

        $euroShippingZone = ShippingZone::create([
            'name' => 'Europe',
            'type' => 'countries',
        ]);

        $euroShippingRate = ShippingRate::create([
            'shipping_zone_id' => $euroShippingZone->id,
            'shipping_method_id' => $euroShipping->id,
            'enabled' => true,
        ]);

        $euroShippingZone->countries()->sync(
            Country::whereIn('iso3', [
                'AUT',
                'BEL',
                'BGR',
                'HRV',
                'CYP',
                'CZE',
                'DNK',
                'EST',
                'FIN',
                'FRA',
                'DEU',
                'GRC',
                'HUN',
                'IRL',
                'ITA',
                'LVA',
                'LTU',
                'LUX',
                'MLT',
                'NLD',
                'POL',
                'ROU',
                'SVK',
                'ESP',
                'SWE',
            ])->pluck('id'),
        );

        Price::create([
            'priceable_type' => (new ShippingRate)->getMorphClass(),
            'priceable_id' => $euroShippingRate->id,
            'price' => 2000,
            'min_quantity' => 1,
            'currency_id' => $currency->id,
        ]);

        // Germany Shipping

        $deShippingZone = ShippingZone::create([
            'name' => 'Germany',
            'type' => 'countries',
        ]);

        $deShippingZone->countries()->sync(
            Country::where('iso3', '=', 'DEU')->first()->id,
        );

        $deStandardShipping = ShippingMethod::create([
            'name' => 'Standard Shipping (DE)',
            'code' => 'DE_STD',
            'enabled' => true,
            'driver' => 'ship-by',
        ]);

        $deStandardRate = ShippingRate::create([
            'shipping_zone_id' => $deShippingZone->id,
            'shipping_method_id' => $deStandardShipping->id,
            'enabled' => true,
        ]);

        Price::create([
            'priceable_type' => (new ShippingRate)->getMorphClass(),
            'priceable_id' => $deStandardRate->id,
            'price' => 495,
            'min_quantity' => 1,
            'currency_id' => $currency->id,
        ]);

        $deExpressShipping = ShippingMethod::create([
            'name' => 'Express Shipping (DE)',
            'code' => 'DE_EXP',
            'enabled' => true,
            'driver' => 'ship-by',
        ]);

        $deExpressRate = ShippingRate::create([
            'shipping_zone_id' => $deShippingZone->id,
            'shipping_method_id' => $deExpressShipping->id,
            'enabled' => true,
        ]);

        Price::create([
            'priceable_type' => (new ShippingRate)->getMorphClass(),
            'priceable_id' => $deExpressRate->id,
            'price' => 995,
            'min_quantity' => 1,
            'currency_id' => $currency->id,
        ]);

    }
}
