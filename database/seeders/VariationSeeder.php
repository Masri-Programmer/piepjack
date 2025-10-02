<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Variation;
use App\Models\VariationOption;
use Illuminate\Database\Seeder;

class VariationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();

        $categories->each(function ($category) {
            $variations = collect([
                Variation::create(['name' => 'Color', 'category_id' => $category->id]),
                Variation::create(['name' => 'Size', 'category_id' => $category->id]),
                Variation::create(['name' => 'Material', 'category_id' => $category->id]),
            ]);

            // Create 3 Variation Options for Each Variation
            $variations->each(function ($variation) {
                VariationOption::factory(3)->create(['variation_id' => $variation->id]);
            });
        });
    }
}
