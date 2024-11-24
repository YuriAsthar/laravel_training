<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();

        $this->freezeSecond();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    /**
     * @dataProvider invalidLoginDataProvider
     */
    public function test_login_endpoint_properly_with_invalid_data(array $errorMessage, array $data): void
    {
        $this->postJson(route('api.auth.login'), $data)
            ->assertJsonValidationErrors($errorMessage)
            ->assertUnprocessable();
    }

    public function test_login_endpoint_properly_with_valid_data(): void
    {
        $password = Str::random();
        $emailVerifiedAt = now();
        $user = User::factory()->create([
            'password' => Hash::make($password),
            'email_verified_at' => $emailVerifiedAt,
        ]);

        $this->postJson(route('api.auth.login'), [
            'email' => $user->email,
            'password' => $password,
        ])
            ->assertJsonPath('data.token_type', 'bearer')
            ->assertJsonPath('data.expires_in', 3600)
            ->assertStatus(201);
    }

    public function test_logout_endpoint_properly(): void
    {
        $user = User::factory()->create();
        $expectedToken = $this->generateJwtToken($user);
        $headers = ['Authorization' => 'Bearer '.$expectedToken];

        $this->postJson(
            route('api.auth.logout'),
            headers: $headers,
        )
            ->assertNoContent();

        $this->getJson(route('api.auth.me'), $headers)
            ->assertStatus(401);
    }

    public function test_me_endpoint_properly(): void
    {
        $user = User::factory()->create();
        $headers = ['Authorization' => 'Bearer '.$this->generateJwtToken($user)];

        $this->getJson(route('api.auth.me'), $headers)
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.email', $user->email)
            ->assertJsonPath('data.email_verified_at', $user->email_verified_at->toIso8601String())
            ->assertStatus(200);
    }

    public function test_refresh_endpoint_properly(): void
    {
        $user = User::factory()->create();
        $oldToken = $this->generateJwtToken($user);
        $headers = ['Authorization' => 'Bearer '.$oldToken];
        $newToken = $this->postJson(route('api.auth.refresh'), $headers)
            ->assertStatus(200)
            ->json('data')['access_token'];

        $this->assertNotEquals($oldToken, $newToken);

        $headers['Authorization'] = 'Bearer '.$newToken;

        $this->getJson(route('api.auth.me'), $headers)
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.email', $user->email)
            ->assertStatus(200);
    }

    public static function invalidLoginDataProvider(): array
    {
        return [
            [
                ['email' => ['The email field is required.']],
                ['password' => Str::random()],
            ],
            [
                ['email' => ['The email field is required.']],
                ['email' => null, 'password' => Str::random()],
            ],
            [
                ['email' => ['The email field must be a valid email address.']],
                [
                    'email' => Str::random(),
                    'password' => Str::random(),
                ],
            ],
            [
                ['password' => ['The password field is required.']],
                ['email' => 'a@yuriasthar.com'],
            ],
            [
                ['password' => ['The password field is required.']],
                ['email' => 'a@yuriasthar.com', 'password' => null],
            ],
        ];
    }

    private function generateJwtToken(User $user, string $password = 'password'): string
    {
        return auth()->attempt(['email' => $user->email, 'password' => $password]);
    }
}
