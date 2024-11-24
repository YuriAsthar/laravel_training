<?php

namespace LaravelTraining\Enums\Models\TravelRequest;

use ArchTech\Enums\From;
use ArchTech\Enums\InvokableCases;
use ArchTech\Enums\Values;

enum Status: string
{
    use Values,
        InvokableCases,
        From;

    case REQUESTED = 'requested';
    case APPROVED = 'approved';
    case CANCELLED = 'cancelled';
}
