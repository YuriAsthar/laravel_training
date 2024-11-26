<?php

namespace App\Policies;

use App\Models\TravelRequest;
use App\Models\User;

class TravelRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, TravelRequest $travelRequest): bool
    {
        return $travelRequest->user()->is($user);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, TravelRequest $travelRequest): bool
    {
        return $travelRequest->user()->is($user);
    }
}
