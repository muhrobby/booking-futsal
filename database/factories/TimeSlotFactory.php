<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TimeSlotFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startTime = $this->faker->time();
        
        return [
            'start_time' => $startTime,
            'end_time' => date('H:i:s', strtotime($startTime . ' +1 hour')),
            'is_active' => true,
        ];
    }
}
