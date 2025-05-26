<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ProductItem;
use App\Models\Product;

class ProductItemFactory extends Factory
{
    protected $model = ProductItem::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'quantity' => $this->faker->numberBetween(1, 100),
            'image' => 'https://www.ktmcty.com/storage/products/unisex-half-sleeve-t-shirt-kurt45402-summer-wear/gallery/thumbs/cover_17121457971_.jpg',
            'image_mime' => 'image/jpeg',
            'image_size' => $this->faker->numberBetween(100, 5000),
            'active' => $this->faker->boolean,
            'price' => $this->faker->randomFloat(2, 10, 1000),
        ];
    }
}
