<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::insert([
            ['key' => 'site_name', 'value' => 'My E-Commerce'],
            ['key' => 'currency', 'value' => 'EUR'],
            ['key' => 'tax_rate', 'value' => '19'],
        ]);
    }
}
