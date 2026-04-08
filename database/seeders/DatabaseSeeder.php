<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            LunarStoreSeeder::class,
            CollectionSeeder::class,
            AttributeSeeder::class,
            TaxSeeder::class,
            ProductSeeder::class,
                // CustomerSeeder::class,
            ShippingSeeder::class,
            // OrderSeeder::class,
        ]);
    }
}
