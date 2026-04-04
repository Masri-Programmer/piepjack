<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Lunar\Models\CollectionGroup;
use Lunar\Models\Collection;
use Lunar\Models\Product as LunarProduct;
use Lunar\Models\ProductVariant;
use Lunar\Models\ProductType;
use Lunar\Models\TaxClass;
use Lunar\Models\Currency;
use Lunar\Models\Price;
use Lunar\FieldTypes\TranslatedText;
use Illuminate\Support\Str;

class LunarProductSeeder extends Seeder
{
    public function run(): void
    {
        $group = CollectionGroup::where('handle', 'main-categories')->first();
        $productType = ProductType::where('name', 'Standard')->first();
        $taxClass = TaxClass::where('handle', 'default')->first();
        $currency = Currency::where('code', 'EUR')->first();

        if (!$group || !$productType || !$taxClass || !$currency) {
            return;
        }

        $categories = [
            'T-Shirts' => ['Heavyweight Oversize T-Shirt', 'Classic Cotton Tee'],
            'Sweaters' => ['Hoodie Black', 'Gray Sweatshirt'],
            'Jackets' => ['Denim Jacket', 'Windbreaker'],
        ];

        foreach ($categories as $catName => $products) {
            // 1. Create Collection
            $collection = Collection::create([
                'collection_group_id' => $group->id,
                'attribute_data' => [
                    'name' => new TranslatedText(collect(['en' => $catName])),
                ],
            ]);

            foreach ($products as $prodName) {
                // 2. Create Product
                $lunarProduct = LunarProduct::create([
                    'product_type_id' => $productType->id,
                    'status' => 'published',
                    'attribute_data' => [
                        'name' => new TranslatedText(collect(['en' => $prodName])),
                        'description' => new TranslatedText(collect(['en' => 'Premium quality ' . $prodName])),
                    ],
                ]);

                // Attach to collection
                $lunarProduct->collections()->attach($collection->id);

                // 3. Create Variants (e.g., S, M, L)
                $sizes = ['S', 'M', 'L'];
                foreach ($sizes as $size) {
                    $variant = ProductVariant::create([
                        'product_id' => $lunarProduct->id,
                        'tax_class_id' => $taxClass->id,
                        'sku' => Str::upper(Str::slug($prodName)) . '-' . $size,
                        'stock' => 100,
                        'purchasable' => 'always',
                        'shippable' => true,
                    ]);

                    // 4. Create Price
                    Price::create([
                        'priceable_type' => ProductVariant::class,
                        'priceable_id' => $variant->id,
                        'currency_id' => $currency->id,
                        'price' => rand(2500, 4500), // Prices in cents
                        'min_quantity' => 1,
                    ]);
                }
            }
        }
    }
}
