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
            // RoleSeeder::class,
            // UserSeeder::class,
            // AddressSeeder::class,

            // New Lunar Seeders
            // LunarCoreSeeder::class,
            // LunarProductSeeder::class,

            // CategorySeeder::class,
            // VariationSeeder::class,
            // ProductSeeder::class,
            // SettingSeeder::class,

            // Product Interaction Seeders (Keep if needed for custom logic)
            // ProductReviewSeeder::class,
            // ProductCommentSeeder::class,
            // SpecificProductSeeder::class,
            LunarStoreSeeder::class,
        ]);
    }
}
