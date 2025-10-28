<?php

namespace Database\Factories;

use App\Models\Field;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'field_id' => Field::factory(),
            'time_slot_id' => TimeSlot::factory(),
            'booking_date' => $this->faker->dateTimeBetween('+1 day', '+30 days')->format('Y-m-d'),
            'customer_name' => $this->faker->name(),
            'customer_phone' => $this->faker->phoneNumber(),
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'canceled']),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
