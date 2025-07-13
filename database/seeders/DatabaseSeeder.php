<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Models\ProductConfiguration;
use App\Models\ProductItem;
use App\Models\Variation;
use App\Models\VariationOption;
use Illuminate\Database\Seeder;
use Database\Seeders\ProductCommentSeeder;
use Database\Seeders\ProductReviewSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            AddressSeeder::class,
        ]);

        $categoryNames = ['t-shirts', 'sweaters', 'jackets', 'sports', 'accessories', 'underwear',];
        $categories = collect();
        foreach ($categoryNames as $name) {
            $categories->push(Category::create(['name' => ucfirst($name), 'active' => true]));
        }

        $categories->each(function ($category) {
            $variations = collect([
                Variation::create(['name' => 'Color', 'category_id' => $category->id]),
                Variation::create(['name' => 'Size', 'category_id' => $category->id]),
                Variation::create(['name' => 'Material', 'category_id' => $category->id]),
            ]);

            // Create 3 Variation Options for Each Variation
            $variationOptions = $variations->mapWithKeys(function ($variation) {
                return [$variation->id => VariationOption::factory(3)->create(['variation_id' => $variation->id])];
            });

            // Create Products
            $products = Product::factory(10)->create(['category_id' => $category->id]);

            // Create Product Items
            $products->each(function ($product) use ($variations, $variationOptions) {
                $productItems = ProductItem::factory(3)->create(['product_id' => $product->id]);

                // Assign Variations to Product Items
                $productItems->each(function ($productItem) use ($variations, $variationOptions) {
                    $variations->each(function ($variation) use ($productItem, $variationOptions) {
                        ProductConfiguration::create([
                            'product_item_id' => $productItem->id,
                            'variation_option_id' => $variationOptions[$variation->id]->random()->id,
                        ]);
                    });
                });
            });
        });

        // Create Settings
        Setting::insert([
            ['key' => 'site_name', 'value' => 'My E-Commerce'],
            ['key' => 'currency', 'value' => 'EUR'],
            ['key' => 'tax_rate', 'value' => '19'], // Example: 19% tax rate
        ]);


        $this->call([
            ProductReviewSeeder::class,
            ProductCommentSeeder::class,
        ]);
    }
}
