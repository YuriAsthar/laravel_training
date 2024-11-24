<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use LaravelTraining\Enums\Models\TravelRequest\Status;

class TravelRequestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'status' => Status::REQUESTED,
        ];
    }
}
