<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function generateJwtToken(User $user, string $password = 'password'): string
    {
        return auth()->attempt(['email' => $user->email, 'password' => $password]);
    }
}
