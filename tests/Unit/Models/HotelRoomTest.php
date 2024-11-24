<?php

namespace Tests\Unit\Models;

use App\Models\Hotel;
use App\Models\HotelRoom;
use App\Models\TravelRequest;
use App\Models\User;
use Illuminate\Database\Console\PruneCommand;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class HotelRoomTest extends TestCase
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

        $hotelRoom = HotelRoom::factory()->for($this->hotel)->create();

        $this->assertDatabaseHas('hotel_rooms', [
            'id' => $hotelRoom->id,
        ]);
    }

    public function test_if_model_relations_properly(): void
    {
        $this->freezeSecond();

        $hotelRoom = HotelRoom::factory()->for($this->hotel)->create();

        $this->assertEquals($this->hotel->fresh(), $hotelRoom->hotel);

        $travelRequest = TravelRequest::factory()->for($this->user)->create();

        $hotelRoom->travelRequests()->attach($travelRequest);

        $this->assertEquals($travelRequest->fresh(), $hotelRoom->travelRequests()->first()->fresh());

        $this->assertDatabaseHas('hotel_room_travel_request', [
            'hotel_room_id' => $hotelRoom->id,
            'travel_request_id' => $travelRequest->id,
        ]);
    }

    public function test_if_it_will_prune_properly(): void
    {
        $this->freezeTime();

        $hotelRoomOne = HotelRoom::factory()->for($this->hotel)->create(['deleted_at' => now()]);
        $hotelRoomTwo = HotelRoom::factory()->for($this->hotel)->create(['deleted_at' => now()->addDays(29)]);
        $hotelRoomThree = HotelRoom::factory()->for($this->hotel)->create(['deleted_at' => now()->subDays(30)]);
        $hotelRoomFour = HotelRoom::factory()->for($this->hotel)->create(['deleted_at' => now()->subDays(90)]);

        $this->artisan(PruneCommand::class, [
            '--model' => HotelRoom::class,
        ])
            ->assertSuccessful();

        $this->assertDatabasehas('hotel_rooms', ['id' => $hotelRoomOne->id]);
        $this->assertDatabasehas('hotel_rooms', ['id' => $hotelRoomTwo->id]);
        $this->assertDatabaseMissing('hotel_rooms', ['id' => $hotelRoomThree->id]);
        $this->assertDatabaseMissing('hotel_rooms', ['id' => $hotelRoomFour->id]);
    }

    public function test_if_soft_deleted_model_properly(): void
    {
        $this->freezeTime();

        $hotelRoom = HotelRoom::factory()->for($this->hotel)->create();

        $hotelRoom->delete();

        $this->assertDatabaseHas('hotel_rooms', [
            'id' => $hotelRoom->id,
            'deleted_at' => now(),
        ]);
    }
}
