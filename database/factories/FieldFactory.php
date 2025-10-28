<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class FieldFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word() . ' Court',
            'location' => $this->faker->city(),
            'description' => $this->faker->sentence(),
            'price_per_hour' => $this->faker->numberBetween(100000, 300000),
            'is_active' => true,
        ];
    }
}
