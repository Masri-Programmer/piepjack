<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductConfiguration;
use App\Models\ProductItem;
use App\Models\VariationOption;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Eager load variations to avoid N+1 queries when accessing $category->variations
        $categories = Category::with('variations')->get();

        $categories->each(function ($category) {
            // Create 10 products for the current category
            $products = Product::factory(10)->create(['category_id' => $category->id]);

            // Get all variation IDs for this category
            $variationIds = $category->variations->pluck('id');

            // Fetch all possible variation options for this category in a single query and group them by their parent variation's ID
            $optionsByVariationId = VariationOption::whereIn('variation_id', $variationIds)
                ->get()
                ->groupBy('variation_id');

            $products->each(function ($product) use ($category, $optionsByVariationId) {
                // Create 3 product items (SKUs) for each product
                $productItems = ProductItem::factory(3)->create(['product_id' => $product->id]);

                // Assign a configuration to each product item
                $productItems->each(function ($productItem) use ($category, $optionsByVariationId) {
                    // For each variation in the category (Color, Size, etc.), assign a random option
                    $category->variations->each(function ($variation) use ($productItem, $optionsByVariationId) {
                        // Check if we have options for this variation before trying to access them
                        if (isset($optionsByVariationId[$variation->id]) && $optionsByVariationId[$variation->id]->isNotEmpty()) {
                            ProductConfiguration::create([
                                'product_item_id' => $productItem->id,
                                'variation_option_id' => $optionsByVariationId[$variation->id]->random()->id,
                            ]);
                        }
                    });
                });
            });
        });
    }
}
