<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class HotelFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'country' => $this->faker->country(),
            'city' => $this->faker->city(),
            'state' => $this->faker->streetSuffix(),
            'neighborhood' => $this->faker->city(),
            'street' => $this->faker->streetName(),
            'number' => $this->faker->randomNumber(4),
        ];
    }
}
