<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Variation;
use App\Models\Category;

class VariationFactory extends Factory
{
    protected $model = Variation::class;

    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => $this->faker->word,
        ];
    }
}
