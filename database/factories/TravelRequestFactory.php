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
            'start_date' => now()->subDays(random_int(1, 30))->timestamp,
            'end_date' => now()->addDays(random_int(1, 30))->timestamp,
        ];
    }
}
