<?php

namespace Database\Factories;

use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Address::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'label' => fake()->randomElement(['Home', 'Work', 'Parents']),
            'is_default_shipping' => false,
            'is_default_billing' => false,
            'street_address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state_province' => fake()->state(),
            'postal_code' => fake()->postcode(),
            'country_code' => fake()->countryCode(),
        ];
    }
}
