<?php

namespace Tests\Unit\Models;

use App\Models\Hotel;
use App\Models\Transport;
use App\Models\User;
use Illuminate\Database\Console\PruneCommand;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class TransportTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    private Hotel $hotel;

    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();

        $this->user = User::factory()->create();
        $this->hotel = Hotel::factory()->create();
    }

    public function test_if_generate_model_factory_properly(): void
    {
        $this->freezeSecond();

        $transport = Transport::factory()->create();

        $this->assertDatabaseHas('transports', [
            'id' => $transport->id,
        ]);
    }

    public function test_if_model_relations_properly(): void
    {
        $this->freezeSecond();

        $transport = Transport::factory()->create();

        $transport->hotels()->attach($this->hotel);

        $this->assertEquals($this->hotel->fresh(), $transport->hotels()->first()->fresh());

        $this->assertDatabaseHas('hotel_transport', [
            'hotel_id' => $this->hotel->id,
            'transport_id' => $transport->id,
        ]);
    }

    public function test_if_it_will_prune_properly(): void
    {
        $this->freezeTime();

        $transportOne = Transport::factory()->create(['deleted_at' => now()]);
        $transportTwo = Transport::factory()->create(['deleted_at' => now()->addDays(29)]);
        $transportThree = Transport::factory()->create(['deleted_at' => now()->subDays(30)]);
        $transportFour = Transport::factory()->create(['deleted_at' => now()->subDays(90)]);

        $this->artisan(PruneCommand::class, [
            '--model' => Transport::class,
        ])
            ->assertSuccessful();

        $this->assertDatabasehas('transports', ['id' => $transportOne->id]);
        $this->assertDatabasehas('transports', ['id' => $transportTwo->id]);
        $this->assertDatabaseMissing('transports', ['id' => $transportThree->id]);
        $this->assertDatabaseMissing('transports', ['id' => $transportFour->id]);
    }

    public function test_if_soft_deleted_model_properly(): void
    {
        $this->freezeTime();

        $transport = Transport::factory()->create();

        $transport->delete();

        $this->assertDatabaseHas('transports', [
            'id' => $transport->id,
            'deleted_at' => now(),
        ]);
    }
}
