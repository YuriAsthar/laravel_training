<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use LaravelTraining\Enums\Models\Terminal\Type;

class TerminalFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'Terminal '.$this->faker->name(),
            'type' => Type::OUTWARD_TRIP,
            'country' => $this->faker->country(),
            'city' => $this->faker->city(),
            'state' => $this->faker->streetSuffix(),
            'neighborhood' => $this->faker->firstName(),
            'street' => $this->faker->streetName(),
            'street_number' => $this->faker->randomNumber(5),
        ];
    }
}

