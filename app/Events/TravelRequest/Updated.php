<?php

namespace App\Events\TravelRequest;

use App\Models\TravelRequest;

class Updated
{
    public function __construct(public TravelRequest $travelRequest)
    {
    }
}
