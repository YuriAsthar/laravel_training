<?php

namespace LaravelTraining\Enums\Models\HotelRoom;

use ArchTech\Enums\From;
use ArchTech\Enums\InvokableCases;
use ArchTech\Enums\Values;

enum Currency: string
{
    use Values, InvokableCases, From;

    case BRL = 'brl';
}
