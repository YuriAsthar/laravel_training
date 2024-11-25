<?php

namespace LaravelTraining\Enums\Models\TravelRequest;

use ArchTech\Enums\From;
use ArchTech\Enums\InvokableCases;
use ArchTech\Enums\Meta\Meta;
use ArchTech\Enums\Metadata;
use ArchTech\Enums\Values;
use LaravelTraining\Enums\Models\TravelRequest\metadata\CanCancel;

#[Meta(CanCancel::class)]
enum Status: string
{
    use Values,
        InvokableCases,
        From,
        Metadata;

    #[CanCancel(true)]
    case REQUESTED = 'requested';
    #[CanCancel(false)]
    case APPROVED = 'approved';
    #[CanCancel(false)]
    case CANCELLED = 'cancelled';

    public static function canSendNotification(string $status): bool
    {
        return match ($status) {
            self::APPROVED(), self::CANCELLED() => true,
            default => false,
        };
    }
}
