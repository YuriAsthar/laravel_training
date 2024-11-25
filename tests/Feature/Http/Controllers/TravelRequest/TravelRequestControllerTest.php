<?php

namespace Tests\Feature\Http\Controllers\TravelRequest;

use App\Models\Hotel;
use App\Models\HotelRoom;
use App\Models\Terminal;
use App\Models\TerminalTransport;
use App\Models\Transport;
use App\Models\TravelRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use LaravelTraining\Enums\Models\Terminal\Type;
use LaravelTraining\Enums\Models\Transport\Type as TransportType;
use LaravelTraining\Enums\Models\TravelRequest\Status;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class TravelRequestControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    private User $anotherUser;

    private array $headers;

    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();

        $this->user = User::factory()->create();
        $this->anotherUser = User::factory()->create();
        $this->headers = ['Authorization' => 'Bearer '.$this->generateJwtToken($this->user)];
    }

    public function test_index_endpoint_properly(): void
    {
        $travelRequestOne = TravelRequest::factory()->for($this->user)->create();
        $travelRequestTwo = TravelRequest::factory()->for($this->user)->create();
        TravelRequest::factory()->for($this->anotherUser)->create();

        $this->getJson(
            route('api.travel-requests.index'),
            $this->headers,
        )
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.id', $travelRequestTwo->id)
            ->assertJsonPath('data.1.id', $travelRequestOne->id)
            ->assertSuccessful();
    }

    public function test_show_endpoint_properly(): void
    {
        $travelRequestOne = TravelRequest::factory()->for($this->user)->create();
        TravelRequest::factory()->for($this->user)->create();

        $this->getJson(
            route('api.travel-requests.show', $travelRequestOne),
            $this->headers,
        )
            ->assertJsonPath('data.id', $travelRequestOne->id)
            ->assertSuccessful();
    }

    public function test_store_endpoint_properly(): void
    {
        $expectedStartDate = now();
        $expectedEndDate = now()->addDay();
        $transport = Transport::factory()->create();
        $terminal = Terminal::factory()->create();

        $terminal->transports()->attach($transport);

        $terminalTransport = TerminalTransport::whereBelongsTo($terminal)->whereBelongsTo($transport)->first();

        $this->postJson(
            route('api.travel-requests.store'),
            [
                'start_date' => $expectedStartDate->timestamp,
                'end_date' => $expectedEndDate->timestamp,
                'terminal_transport_id' => $terminalTransport->id,
                'hotel_room_ids' => [],
            ],
            $this->headers,
        )
            ->assertJsonPath('data.start_date', $expectedStartDate->toIso8601String())
            ->assertJsonPath('data.end_date', $expectedEndDate->toIso8601String())
            ->assertJsonPath('data.user_id', $this->user->id)
            ->assertJsonPath('data.status', Status::REQUESTED())
            ->assertSuccessful();
    }

    #[DataProvider('storeEndpointInvalidDataProvider')]
    public function test_store_endpoint_return_422_properly(array $invalidData, array $errorMessage): void
    {
        $this->postJson(
            route('api.travel-requests.store'),
            $invalidData,
            $this->headers,
        )
            ->assertUnprocessable()
            ->assertJsonValidationErrors($errorMessage);
    }

    public static function storeEndpointInvalidDataProvider(): array
    {
        return [
            [
                [
                    'start_date' => now()->addDay()->timestamp,
                    'end_date' => now()->timestamp,
                    'terminal_transport_id' => 1,
                    'hotel_room_ids' => [],
                ],
                [
                    'terminal_transport_id' => [
                        'O terminal com este transporte não está mais disponível no momento!',
                        'A data de inicio da viagem não pode ser maior que a final!',
                    ],
                ],
            ],
            [
                [
                    'start_date' => now()->timestamp,
                    'end_date' => now()->addDay()->timestamp,
                    'terminal_transport_id' => 1,
                ],
                [
                    'hotel_room_ids' => ['The hotel room ids field must be present.'],
                ],
            ],
            [
                [
                    'start_date' => now()->timestamp,
                    'end_date' => now()->addDay()->timestamp,
                    'hotel_room_ids' => [],
                ],
                [
                    'terminal_transport_id' => ['The terminal transport id field is required.'],
                ],
            ],
        ];
    }

    public function test_update_endpoint_properly(): void
    {
        $expectedStatus = Status::APPROVED();
        $travelRequest = TravelRequest::factory()->for($this->user)->create();

        $this->putJson(
            route('api.travel-requests.update', $travelRequest),
            [
                'status' => $expectedStatus,
            ],
            $this->headers,
        )
            ->assertJsonPath('data.id', $travelRequest->id)
            ->assertJsonPath('data.status', $expectedStatus)
            ->assertSuccessful();
    }

    #[DataProvider('updateEndpointInvalidDataProvider')]
    public function test_update_endpoint_return_422_properly(Status $invalidStatus, array $errorMessage): void
    {
        $travelRequest = TravelRequest::factory()->for($this->user)->create(['status' => Status::CANCELLED]);

        $this->putJson(
            route('api.travel-requests.update', $travelRequest),
            [
                'status' => $invalidStatus,
            ],
            $this->headers,
        )
            ->assertJsonValidationErrors($errorMessage)
            ->assertUnprocessable();
    }

    public static function updateEndpointInvalidDataProvider(): array
    {
        return [
            [
                Status::CANCELLED,
                ['terminal_transport_id' => ['Não é possível alterar o status de uma solicitação de viagem quando o status atual é cancelado!']],
            ],
            [
                Status::APPROVED,
                ['terminal_transport_id' => ['Não é possível alterar o status de uma solicitação de viagem quando o status atual é cancelado!']],
            ],
        ];
    }

    #[DataProvider('filterProvider')]
    public function test_index_endpoint_properly_with_filter(
        array $filter,
        callable $function,
    ): void {
        [
            'expected_travel_request' => $expectedTravelRequest,
        ] = $function($this);

        $this->getJson(
            route('api.travel-requests.index', $filter),
            $this->headers,
        )
            ->assertSuccessful()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $expectedTravelRequest->id);
    }

    public static function filterProvider(): array
    {
        $defaultTimestamp = 1732488785;

        return [
            [
                ['filter[status]' => Status::REQUESTED()],
                function (self $test) {
                    TravelRequest::factory()->for($test->user)->create(['status' => Status::CANCELLED]);
                    TravelRequest::factory()->for($test->user)->create(['status' => Status::APPROVED]);

                    return [
                        'expected_travel_request' => TravelRequest::factory()->for($test->user)->create(['status' => Status::REQUESTED]),
                    ];
                },
            ],
            [
                ['filter[hotel_name]' => 'Ssj 2'],
                function (self $test) {
                    self::createHotel($test, Str::uuid()->toString());

                    $expectedHotelName = 'Ssj 2';
                    $expectedTravelRequest = self::createHotel($test, $expectedHotelName);

                    return [
                        'expected_travel_request' => $expectedTravelRequest,
                    ];
                },
            ],
            [
                ['filter[terminal_type]' => Type::RETURN_TRIP()],
                function (self $test) {
                    self::createHotel($test, Str::uuid()->toString());
                    $expectedTravelRequest = self::createHotel($test, Str::uuid()->toString(), Type::RETURN_TRIP);

                    return [
                        'expected_travel_request' => $expectedTravelRequest,
                    ];
                },
            ],
            [
                ['filter[transport_type]' => TransportType::BUS()],
                function (self $test) {
                    self::createHotel($test, Str::uuid()->toString());
                    $expectedTravelRequest = self::createHotel(
                        $test,
                        Str::uuid()->toString(),
                        transportType: TransportType::BUS,
                    );

                    return [
                        'expected_travel_request' => $expectedTravelRequest,
                    ];
                },
            ],
            [
                ['filter[start_date_greater_than]' => $defaultTimestamp],
                function (self $test) use ($defaultTimestamp) {
                    TravelRequest::factory()->for($test->user)->create([
                        'start_date' => Carbon::createFromTimestamp($defaultTimestamp)->subDay(),
                    ]);
                    $expectedTravelRequest = TravelRequest::factory()->for($test->user)->create([
                        'start_date' => Carbon::createFromTimestamp($defaultTimestamp)->addDay(),
                    ]);

                    return [
                        'expected_travel_request' => $expectedTravelRequest,
                    ];
                },
            ],
            [
                ['filter[start_date_less_than]' => $defaultTimestamp],
                function (self $test) use ($defaultTimestamp) {
                    $expectedTravelRequest = TravelRequest::factory()->for($test->user)->create([
                        'start_date' => Carbon::createFromTimestamp($defaultTimestamp)->subDay(),
                    ]);
                     TravelRequest::factory()->for($test->user)->create([
                        'start_date' => Carbon::createFromTimestamp($defaultTimestamp)->addDay(),
                    ]);

                    return [
                        'expected_travel_request' => $expectedTravelRequest,
                    ];
                },
            ],
            [
                ['filter[end_date_greater_than]' => $defaultTimestamp],
                function (self $test) use ($defaultTimestamp) {
                    TravelRequest::factory()->for($test->user)->create([
                        'end_date' => Carbon::createFromTimestamp($defaultTimestamp)->subDay(),
                    ]);
                    $expectedTravelRequest = TravelRequest::factory()->for($test->user)->create([
                        'end_date' => Carbon::createFromTimestamp($defaultTimestamp)->addDay(),
                    ]);

                    return [
                        'expected_travel_request' => $expectedTravelRequest,
                    ];
                },
            ],
            [
                ['filter[end_date_less_than]' => $defaultTimestamp],
                function (self $test) use ($defaultTimestamp) {
                    $expectedTravelRequest = TravelRequest::factory()->for($test->user)->create([
                        'end_date' => Carbon::createFromTimestamp($defaultTimestamp)->subDay(),
                    ]);
                    TravelRequest::factory()->for($test->user)->create([
                        'end_date' => Carbon::createFromTimestamp($defaultTimestamp)->addDay(),
                    ]);

                    return [
                        'expected_travel_request' => $expectedTravelRequest,
                    ];
                },
            ],
            [
                ['filter[created_at_greater_than]' => $defaultTimestamp],
                function (self $test) use ($defaultTimestamp) {
                    TravelRequest::factory()->for($test->user)->create([
                        'created_at' => Carbon::createFromTimestamp($defaultTimestamp)->subDay(),
                    ]);
                    $expectedTravelRequest = TravelRequest::factory()->for($test->user)->create([
                        'created_at' => Carbon::createFromTimestamp($defaultTimestamp)->addDay(),
                    ]);

                    return [
                        'expected_travel_request' => $expectedTravelRequest,
                    ];
                },
            ],
            [
                ['filter[created_at_less_than]' => $defaultTimestamp],
                function (self $test) use ($defaultTimestamp) {
                    $expectedTravelRequest = TravelRequest::factory()->for($test->user)->create([
                        'created_at' => Carbon::createFromTimestamp($defaultTimestamp)->subDay(),
                    ]);
                    TravelRequest::factory()->for($test->user)->create([
                        'created_at' => Carbon::createFromTimestamp($defaultTimestamp)->addDay(),
                    ]);

                    return [
                        'expected_travel_request' => $expectedTravelRequest,
                    ];
                },
            ],
        ];
    }

    private static function createHotel(
        self $test,
        ?string $hotelName = null,
        Type $terminalType = Type::OUTWARD_TRIP,
        TransportType $transportType = TransportType::AIRCRAFT,
    ): TravelRequest {
        $hotelName ??= Str::uuid()->toString();
        $transport = Transport::factory()->create(['type' => $transportType]);
        $terminal = Terminal::factory()->create(['type' => $terminalType]);

        $terminal->transports()->attach($transport);

        $hotel = Hotel::factory()->create(['name' => $hotelName]);

        $hotel->transports()->attach($transport);

        $travelRequest = TravelRequest::factory()
            ->for($test->user)
            ->for(TerminalTransport::whereBelongsTo($transport)->whereBelongsTo($terminal)->first())
            ->create();

        $hotelRoom = HotelRoom::factory()->for($hotel)->create();

        $travelRequest->hotelRooms()->attach($hotelRoom);

        return $travelRequest;
    }
}
