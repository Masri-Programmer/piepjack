<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Lunar\Models\Country;
use Lunar\Models\TaxClass;
use Lunar\Models\TaxRate;
use Lunar\Models\TaxZone;
use Lunar\Models\TaxZoneCountry;

class TaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $taxClass = TaxClass::getDefault() ?? TaxClass::first();

        if (! $taxClass) {
            $taxClass = TaxClass::create([
                'name' => 'Default Tax Class',
                'default' => true,
            ]);
        }

        // UK VAT
        $ukCountry = Country::where('iso3', 'GBR')->first();

        if ($ukCountry) {
            $ukTaxZone = TaxZone::create([
                'name' => 'UK',
                'active' => true,
                'default' => true,
                'zone_type' => 'country',
                'price_display' => 'tax_inclusive',
            ]);

            TaxZoneCountry::create([
                'country_id' => $ukCountry->id,
                'tax_zone_id' => $ukTaxZone->id,
            ]);

            $ukRate = TaxRate::create([
                'name' => 'VAT',
                'tax_zone_id' => $ukTaxZone->id,
                'priority' => 1,
            ]);

            $ukRate->taxRateAmounts()->createMany([
                [
                    'percentage' => 20,
                    'tax_class_id' => $taxClass->id,
                ],
            ]);
        }

        // Germany VAT
        $deCountry = Country::where('iso3', 'DEU')->first();
        if ($deCountry) {
            $deTaxZone = TaxZone::create([
                'name' => 'Germany',
                'active' => true,
                'default' => false,
                'zone_type' => 'country',
                'price_display' => 'tax_inclusive',
            ]);

            TaxZoneCountry::create([
                'country_id' => $deCountry->id,
                'tax_zone_id' => $deTaxZone->id,
            ]);

            $deRate = TaxRate::create([
                'name' => 'Standard VAT',
                'tax_zone_id' => $deTaxZone->id,
                'priority' => 1,
            ]);

            $deRate->taxRateAmounts()->createMany([
                [
                    'percentage' => 19,
                    'tax_class_id' => $taxClass->id,
                ],
            ]);
        }
    }
}
