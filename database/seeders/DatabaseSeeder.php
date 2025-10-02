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
            // Core Auth and User Seeders
            RoleSeeder::class,
            UserSeeder::class,
            AddressSeeder::class,

            // E-commerce Structure Seeders
            CategorySeeder::class,
            VariationSeeder::class,
            ProductSeeder::class,
            SettingSeeder::class,

            // Product Interaction Seeders
            ProductReviewSeeder::class,
            ProductCommentSeeder::class,
            SpecificProductSeeder::class,
        ]);
    }
}
