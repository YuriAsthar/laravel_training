<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use LaravelTraining\Enums\Models\Transport\Type;

class TransportFactory extends Factory
{
    public function definition(): array
    {
        return [
            'type' => Type::OUTWARD_TRIP,
            'name' => 'Hotel '.$this->faker->name(),
        ];
    }
}

