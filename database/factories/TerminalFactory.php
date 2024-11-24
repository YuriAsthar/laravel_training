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
            'country' => 'BRA',
            'city' => 'Betim',
            'state' => 'MG',
            'neighborhood' => 'Central',
            'street' => 'Rua principal ssj',
            'street_number' => '2134',
        ];
    }
}

