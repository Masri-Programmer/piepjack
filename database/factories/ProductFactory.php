<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = $this->faker->words(3, true);

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'image' => 'https://placehold.co/600x400.png?text=Product+Image',
            'image_mime' => 'image/jpeg',
            'image_size' => rand(100, 5000),
            'description' => $this->faker->paragraph,
            'active' => 1,
            'category_id' => Category::factory(),
        ];
    }
}
