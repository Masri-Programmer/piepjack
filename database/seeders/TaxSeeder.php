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
        $taxClass = TaxClass::first();

        // UK VAT
        $ukCountry = Country::firstWhere('iso3', 'GBR');

        $ukTaxZone = TaxZone::factory()->create([
            'name' => 'UK',
            'active' => true,
            'default' => true,
            'zone_type' => 'country',
        ]);

        TaxZoneCountry::factory()->create([
            'country_id' => $ukCountry->id,
            'tax_zone_id' => $ukTaxZone->id,
        ]);

        $ukRate = TaxRate::factory()->create([
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

        // Germany VAT
        $deCountry = Country::firstWhere('iso3', 'DEU');
        if ($deCountry) {
            $deTaxZone = TaxZone::factory()->create([
                'name' => 'Germany',
                'active' => true,
                'default' => false,
                'zone_type' => 'country',
            ]);

            TaxZoneCountry::factory()->create([
                'country_id' => $deCountry->id,
                'tax_zone_id' => $deTaxZone->id,
            ]);

            $deRate = TaxRate::factory()->create([
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
