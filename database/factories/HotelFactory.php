<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class HotelFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'Hotel Corp. Capsule',
            'country' => 'Brasil',
            'city' => 'Betim',
            'state' => 'MG',
            'neighborhood' => 'Centro',
            'street' => 'Av Amazonas',
            'number' => '123',
        ];
    }
}
