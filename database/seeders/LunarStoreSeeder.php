<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Lunar\Models\Channel;
use Lunar\Models\Currency;
use Lunar\Models\Language;
use Lunar\Models\ProductOption;
use Lunar\Models\TaxClass;

class LunarStoreSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Webstore Channel
        Channel::updateOrCreate([
            'handle' => 'webstore',
        ], [
            'name' => 'Webstore',
            'default' => true,
            'url' => config('app.url', 'http://localhost'),
        ]);

        // 2. Languages
        Language::updateOrCreate(['code' => 'de'], [
            'name' => 'German',
            'default' => true,
        ]);
        Language::updateOrCreate(['code' => 'en'], [
            'name' => 'English',
            'default' => false,
        ]);

        // 3. Default Currency (EUR)
        Currency::updateOrCreate(['code' => 'EUR'], [
            'name' => 'Euro',
            'decimal_places' => 2,
            'default' => true,
            'enabled' => true,
            'exchange_rate' => 1,
        ]);

        // 4. Default Tax Class
        TaxClass::firstOrCreate(['name' => 'Default Tax Class'], ['default' => true]);

        // 5. Default Shared Options
        $options = ['Size', 'Color', 'Material'];
        foreach ($options as $opt) {
            ProductOption::updateOrCreate([
                'handle' => Str::slug($opt),
            ], [
                'name' => ['de' => $opt, 'en' => $opt],
                'label' => ['de' => $opt, 'en' => $opt],
                'shared' => true,
            ]);
        }
    }
}
