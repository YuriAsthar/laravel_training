<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use LaravelTraining\Enums\Models\HotelRoom\Currency;

class HotelRoomFactory extends Factory
{
    public function definition(): array
    {
        return [
            'room_number' => $this->faker->randomDigit(),
            'floor_number' => $this->faker->randomDigit(),
            'bed_quantity' => $this->faker->randomDigit(),
            'amount' => random_int(100, 10000),
            'currency' => Currency::BRL,
        ];
    }
}
