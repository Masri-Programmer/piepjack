<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductConfiguration;
use App\Models\ProductItem;
use App\Models\Variation;
use App\Models\VariationOption;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class SpecificProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Setup Category
        $category = Category::firstOrCreate(['name' => 'T-Shirts', 'active' => true]);

        // 2. Define the variations and their options for this category
        $variations = [
            'Size'  => ['S', 'M', 'L', 'XL'],
            'Color' => ['Black', 'White', 'Heather Gray'],
        ];

        $optionsCollection = new Collection();

        foreach ($variations as $variationName => $options) {
            $variationModel = Variation::firstOrCreate([
                'name'        => $variationName,
                'category_id' => $category->id,
            ]);

            $optionModels = [];
            foreach ($options as $optionValue) {
                $optionModels[] = VariationOption::firstOrCreate([
                    'value'        => $optionValue,
                    'variation_id' => $variationModel->id,
                ]);
            }
            $optionsCollection->push($optionModels);
        }

        // 3. Create the main product (with a more generic name)
        $description = <<<HTML
        <ul>
            <li>Oversize fit</li>
            <li>Drop Shoulder</li>
            <li>Gut anliegender Kragen</li>
            <li>Hoher Tragekomfort</li>
            <li>Dichtgewebte Heavyweight Baumwolle - 230g/m²</li>
            <li>100% Baumwolle</li>
            <li>Made in Turkey</li>
        </ul>
        <p>Elias ist 182cm groß und trägt M.</p>
        HTML;

        $product = Product::create([
            'name'        => 'Heavyweight Oversize T-Shirt',
            'description' => $description,
            'category_id' => $category->id,
            'active'      => true,
            'image'       => 'https://placehold.co/600x400.png?text=Product+Image',
        ]);

        // 4. Generate all possible combinations (Cartesian product) of the variation options
        $combinations = $this->getCombinations($optionsCollection->all());

        // 5. Create a unique ProductItem for each combination
        foreach ($combinations as $combination) {
            $productItem = ProductItem::create([
                'product_id' => $product->id,
                'quantity'   => 100,
                'price'      => 39.00,
                'active'     => true,
            ]);

            // 6. Link this ProductItem to its specific variation options
            foreach ($combination as $variationOption) {
                ProductConfiguration::create([
                    'product_item_id'     => $productItem->id,
                    'variation_option_id' => $variationOption->id,
                ]);
            }
        }
    }

    /**
     * Create a Cartesian product of the given arrays of options.
     *
     * @param array $arrays
     * @return array
     */
    private function getCombinations(array $arrays): array
    {
        $result = [[]];
        foreach ($arrays as $key => $values) {
            $append = [];
            foreach ($result as $product) {
                foreach ($values as $item) {
                    $product[$key] = $item;
                    $append[] = $product;
                }
            }
            $result = $append;
        }
        return $result;
    }
}
