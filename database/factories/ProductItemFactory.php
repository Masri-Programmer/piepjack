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
            'quantity' => fake()->numberBetween(1, 100),
            'image' => 'https://placehold.co/600x400.png?text=Product+Image',
            'image_mime' => 'image/jpeg',
            'image_size' => fake()->numberBetween(100, 5000),
            'active' => fake()->boolean(),
            'price' => fake()->randomFloat(2, 10, 1000),
        ];
    }
}
