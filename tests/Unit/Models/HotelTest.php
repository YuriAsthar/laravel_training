<?php

namespace Tests\Unit\Models;

use App\Models\Hotel;
use App\Models\HotelRoom;
use App\Models\User;
use Illuminate\Database\Console\PruneCommand;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class HotelTest extends TestCase
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

        $hotel = Hotel::factory()->create();

        $this->assertDatabaseHas('hotels', [
            'id' => $hotel->id,
        ]);
    }

    public function test_if_model_relations_properly(): void
    {
        $this->freezeSecond();

        $hotel = Hotel::factory()->create();
        $hotelRoom = HotelRoom::factory()->for($hotel)->create();

        $this->assertEquals($hotelRoom->fresh(), $hotel->hotelRooms()->first());
    }

    public function test_if_it_will_prune_properly(): void
    {
        $this->freezeTime();

        $hotelOne = Hotel::factory()->create(['deleted_at' => now()]);
        $hotelTwo = Hotel::factory()->create(['deleted_at' => now()->addDays(29)]);
        $hotelThree = Hotel::factory()->create(['deleted_at' => now()->subDays(30)]);
        $hotelFour = Hotel::factory()->create(['deleted_at' => now()->subDays(90)]);

        $this->artisan(PruneCommand::class, [
            '--model' => Hotel::class,
        ])
            ->assertSuccessful();

        $this->assertDatabasehas('hotels', ['id' => $hotelOne->id]);
        $this->assertDatabasehas('hotels', ['id' => $hotelTwo->id]);
        $this->assertDatabaseMissing('hotels', ['id' => $hotelThree->id]);
        $this->assertDatabaseMissing('hotels', ['id' => $hotelFour->id]);
    }

    public function test_if_soft_deleted_model_properly(): void
    {
        $this->freezeTime();

        $hotel = Hotel::factory()->create();

        $hotel->delete();

        $this->assertDatabaseHas('hotels', [
            'id' => $hotel->id,
            'deleted_at' => now(),
        ]);
    }
}
