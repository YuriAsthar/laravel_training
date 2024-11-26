<?php

namespace Feature\Http\Controllers\Hotel;

use App\Models\Hotel;
use App\Models\HotelRoom;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class HotelControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    private array $headers;

    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();

        $this->user = User::factory()->create();
        $this->headers = ['Authorization' => 'Bearer '.$this->generateJwtToken($this->user)];
    }

    public function test_index_endpoint_properly(): void
    {
        $hotel = Hotel::factory()->create();
        $hotelRoomOne = HotelRoom::factory()->for($hotel)->create();
        $hotelRoomTwo = HotelRoom::factory()->for($hotel)->create();

        $hotelRooms = $this->getJson(
            route('api.internal.hotels.index', $this->headers)
        )
            ->assertJsonPath('data.0.id', $hotel->id)
            ->assertJsonPath('data.0.name', $hotel->name)
            ->assertSuccessful()
            ->json('data')[0]['hotel_rooms'];

        $this->assertEqualsCanonicalizing(
            [
                [
                    'id' => $hotelRoomOne->id,
                    'room_number' => $hotelRoomOne->room_number,
                    'floor_number' => $hotelRoomOne->floor_number,
                    'bed_quantity' => $hotelRoomOne->bed_quantity,
                    'amount' => $hotelRoomOne->amount,
                    'currency' => $hotelRoomOne->currency->value,
                    'hotel_id' => $hotelRoomOne->hotel_id,
                    'created_at' => $hotelRoomOne->created_at->toIso8601String(),
                    'deleted_at' => null,
                ],
                [
                    'id' => $hotelRoomTwo->id,
                    'room_number' => $hotelRoomTwo->room_number,
                    'floor_number' => $hotelRoomTwo->floor_number,
                    'bed_quantity' => $hotelRoomTwo->bed_quantity,
                    'amount' => $hotelRoomTwo->amount,
                    'currency' => $hotelRoomTwo->currency->value,
                    'hotel_id' => $hotelRoomTwo->hotel_id,
                    'created_at' => $hotelRoomTwo->created_at->toIso8601String(),
                    'deleted_at' => null,
                ],
            ],
            $hotelRooms,
        );
    }
}
