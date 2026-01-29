<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\VariationOption;
use App\Models\Variation;

class VariationOptionFactory extends Factory
{
    protected $model = VariationOption::class;

    public function definition(): array
    {
        return [
            'variation_id' => Variation::factory(),
            'value' => $this->faker->word,
        ];
    }
}
