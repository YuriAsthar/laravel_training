<?php

namespace Tests\Unit\Policy;

use App\Models\TravelRequest;
use App\Models\User;
use App\Policies\TravelRequestPolicy;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class TravelRequestPolicyTest extends TestCase
{
    private User $user;

    private TravelRequest $travelRequest;

    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();

        $this->user = User::factory()->create();
        $this->travelRequest = TravelRequest::factory()->for($this->user)->create();
    }

    public function test_if_policy_are_registered(): void
    {
        $this->assertInstanceOf(TravelRequestPolicy::class, policy(TravelRequest::class));
    }

    #[DataProvider('travelRequestPolicyMethodsWithoutParamProvider')]
    public function test_if_policy_method_without_param_return_true_properly(string $policyMethod): void
    {
        $this->assertTrue(app(TravelRequestPolicy::class)->$policyMethod($this->user));
    }

    #[DataProvider('travelRequestPolicyMethodsWithParamProvider')]
    public function test_if_policy_method_with_param_return_true_properly(string $policyMethod): void
    {
        $this->assertTrue(app(TravelRequestPolicy::class)->$policyMethod($this->user, $this->travelRequest));
    }

    #[DataProvider('travelRequestPolicyMethodsWithParamProvider')]
    public function test_if_policy_method_with_param_return_false_with_invalid_user(string $policyMethod): void
    {
        $this->assertFalse(app(TravelRequestPolicy::class)->$policyMethod(User::factory()->create(), $this->travelRequest));
    }

    #[DataProvider('endpointWithoutTravelRequestParamProvider')]
    public function test_if_endpoint_call_correctly_policy_method_without_travel_request_param(string $policyMethod, string $uriMethod, string $uri): void
    {
        $headers = ['Authorization' => 'Bearer '.$this->generateJwtToken($this->user)];

        $this->partialMock(TravelRequestPolicy::class)->shouldReceive($policyMethod)->once();

        $this->json($uriMethod, route($uri), headers: $headers);
    }

    #[DataProvider('endpointWithTravelRequestParamProvider')]
    public function test_if_endpoint_call_correctly_policy_method_with_travel_request_param(string $policyMethod, string $uriMethod, string $uri): void
    {
        $headers = ['Authorization' => 'Bearer '.$this->generateJwtToken($this->user)];

        $this->partialMock(TravelRequestPolicy::class)->shouldReceive($policyMethod)->once();

        $this->json($uriMethod, route($uri, $this->travelRequest), ['terminal_transport_id' => 123], $headers);
    }

    public static function travelRequestPolicyMethodsWithoutParamProvider(): array
    {
        return [
            ['viewAny'],
            ['create'],
        ];
    }

    public static function travelRequestPolicyMethodsWithParamProvider(): array
    {
        return [
            ['view'],
            ['update'],
        ];
    }

    public static function endpointWithoutTravelRequestParamProvider(): array
    {
        return [
            ['viewAny', 'get', 'api.travel-requests.index'],
            ['create', 'post', 'api.travel-requests.store'],
        ];
    }

    public static function endpointWithTravelRequestParamProvider(): array
    {
        return [
            ['view', 'get', 'api.travel-requests.show'],
            ['update', 'put', 'api.travel-requests.update'],
        ];
    }
}
