<?php

namespace LaravelTraining\Enums\Models\Terminal;

use ArchTech\Enums\From;
use ArchTech\Enums\InvokableCases;
use ArchTech\Enums\Values;

enum Type: string
{
    use Values,
        InvokableCases,
        From;

    case AIRCRAFT = 'aircraft';
    case BUS = 'bus';
}
