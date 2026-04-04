<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Lunar\Models\Currency;
use Lunar\Models\Language;
use Lunar\Models\Channel;
use Lunar\Models\ProductType;
use Lunar\Models\TaxClass;
use Lunar\Models\CollectionGroup;

class LunarCoreSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Languages
        Language::firstOrCreate(['code' => 'en'], [
            'name' => 'English',
            'default' => true,
        ]);

        // 2. Currencies
        Currency::firstOrCreate(['code' => 'EUR'], [
            'name' => 'Euro',
            'exchange_rate' => 1.0,
            'decimal_places' => 2,
            'default' => true,
            'enabled' => true,
        ]);

        // 3. Channels
        Channel::firstOrCreate(['handle' => 'default'], [
            'name' => 'Default Channel',
            'default' => true,
        ]);

        // 4. Product Types
        ProductType::firstOrCreate(['name' => 'Standard'], [
            'name' => 'Standard',
        ]);

        // 5. Tax Classes
        TaxClass::firstOrCreate(['handle' => 'default'], [
            'name' => 'Default Tax Class',
        ]);

        // 6. Collection Groups
        CollectionGroup::firstOrCreate(['handle' => 'main-categories'], [
            'name' => 'Main Categories',
        ]);
    }
}
