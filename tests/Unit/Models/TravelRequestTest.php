<?php

namespace Tests\Unit\Models;

use App\Models\TravelRequest;
use App\Models\User;
use Illuminate\Database\Console\PruneCommand;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class TravelRequestTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();

        $this->user = User::factory()->create();
    }

    public function test_if_generate_model_factory_properly(): void
    {
        $this->freezeSecond();

        $travelRequest = TravelRequest::factory()->for($this->user)->create();

        $this->assertDatabaseHas('travel_requests', [
            'id' => $travelRequest->id,
            'user_id' => $this->user->id,
            'created_at' => now(),
        ]);
    }

    public function test_if_model_relations_properly(): void
    {
        $this->freezeSecond();

        $travelRequest = TravelRequest::factory()->for($this->user)->create();

        $this->assertEquals($this->user->fresh(), $travelRequest->user);
        $this->assertEquals($travelRequest->fresh(), $this->user->travelRequests()->first());
    }

    public function test_if_it_will_prune_properly(): void
    {
        $this->freezeTime();

        $travelRequestOne = TravelRequest::factory()->for($this->user)->create(['deleted_at' => now()]);
        $travelRequestTwo = TravelRequest::factory()->for($this->user)->create(['deleted_at' => now()->addDays(29)]);
        $travelRequestThree = TravelRequest::factory()->for($this->user)->create(['deleted_at' => now()->subDays(30)]);
        $travelRequestFour = TravelRequest::factory()->for($this->user)->create(['deleted_at' => now()->subDays(90)]);

        $this->artisan(PruneCommand::class, [
            '--model' => TravelRequest::class,
        ])
            ->assertSuccessful();

        $this->assertDatabasehas('travel_requests', ['id' => $travelRequestOne->id]);
        $this->assertDatabasehas('travel_requests', ['id' => $travelRequestTwo->id]);
        $this->assertDatabaseMissing('travel_requests', ['id' => $travelRequestThree->id]);
        $this->assertDatabaseMissing('travel_requests', ['id' => $travelRequestFour->id]);
    }

    public function test_if_soft_deleted_model_properly(): void
    {
        $this->freezeTime();

        $travelRequest = TravelRequest::factory()->for($this->user)->create();

        $travelRequest->delete();

        $this->assertDatabaseHas('travel_requests', [
            'id' => $travelRequest->id,
            'deleted_at' => now(),
        ]);
    }
}
