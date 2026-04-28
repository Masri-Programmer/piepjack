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
     * Stripe-supported European countries (ISO-3 format for Lunar)
     */
    
    protected const STRIPE_EUROPE_ISO3 = [
        'AUT', 'BEL', 'BGR', 'HRV', 'CYP', 'CZE', 'DNK', 'EST', 'FIN', 'FRA', 'DEU', 
        'GRC', 'HUN', 'IRL', 'ITA', 'LVA', 'LIE', 'LTU', 'LUX', 'MLT', 'NLD', 'NOR', 
        'POL', 'PRT', 'ROU', 'SVK', 'SVN', 'ESP', 'SWE', 'CHE', 'GBR', 'GIB'
    ];

    /**
     * Allowed methods to seed.
     * We store the Sendcloud carrier in the 'data' JSON column so the modifier can use it.
     */
    protected const SEED_METHODS = [
        ['name' => 'DHL Paket', 'code' => 'DHL_PAKET', 'carrier' => 'dhl_de', 'fallback_price' => 590],
        ['name' => 'DHL Paket International', 'code' => 'DHL_PAKET_INT', 'carrier' => 'dhl_de', 'fallback_price' => 1290],
        ['name' => 'DPD Classic', 'code' => 'DPD_CLASSIC', 'carrier' => 'dpd', 'fallback_price' => 490],
        ['name' => 'DPD Business Express', 'code' => 'DPD_EXPRESS', 'carrier' => 'dpd', 'fallback_price' => 990],
    ];

    public function run(): void
    {
        $currency = Currency::getDefault();

        // 1. Create the Stripe Europe Shipping Zone
        $europeZone = ShippingZone::firstOrCreate([
            'name' => 'Stripe Europe',
            'type' => 'countries',
        ]);

        // Sync the allowed European countries to this zone
        $countryIds = Country::whereIn('iso3', self::STRIPE_EUROPE_ISO3)->pluck('id');
        $europeZone->countries()->sync($countryIds);

        // 2. Seed the Shipping Methods and Rates
        foreach (self::SEED_METHODS as $methodData) {
            $shippingMethod = ShippingMethod::firstOrCreate(
                ['code' => $methodData['code']],
                [
                    'name' => $methodData['name'],
                    'enabled' => true,
                    'driver' => 'sendcloud', // Custom driver identifier
                    'data' => [
                        'carrier' => $methodData['carrier'],
                    ],
                ]
            );

            $shippingRate = ShippingRate::firstOrCreate([
                'shipping_zone_id' => $europeZone->id,
                'shipping_method_id' => $shippingMethod->id,
            ], [
                'enabled' => true,
            ]);

            // Add the fallback price (which you can now edit in the Lunar Admin panel)
            if ($shippingRate->wasRecentlyCreated || $shippingRate->prices->isEmpty()) {
                Price::create([
                    'priceable_type' => (new ShippingRate)->getMorphClass(),
                    'priceable_id' => $shippingRate->id,
                    'price' => $methodData['fallback_price'],
                    'min_quantity' => 1,
                    'currency_id' => $currency->id,
                ]);
            }
        }
    }
}