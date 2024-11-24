<?php

namespace Database\Seeders;

use App\Models\Hotel;
use App\Models\HotelRoom;
use App\Models\Terminal;
use App\Models\TerminalTransport;
use App\Models\Transport;
use App\Models\TravelRequest;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use LaravelTraining\Enums\Models\Terminal\Type;
use LaravelTraining\Enums\Models\Transport\Type as TransportType;
use LaravelTraining\Enums\Models\TravelRequest\Status;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Transport::factory()
            ->createMany([
                ['type' => TransportType::AIRCRAFT],
                ['type' => TransportType::BUS],
            ])
            ->each(function (Transport $transport) {
                Terminal::factory()
                    ->createMany([
                        ['type' => Type::OUTWARD_TRIP],
                        ['type' => Type::RETURN_TRIP],
                    ])
                    ->each(fn (Terminal $terminal) => $terminal->transports()->attach($transport));

                Hotel::factory(2)
                    ->create()
                    ->each(function (Hotel $hotel) use ($transport) {
                        $hotel->transports()->attach($transport);
                        HotelRoom::factory(3)->for($hotel)->create();
                    });
            });

        User::factory()
            ->createMany([
                [
                    'name' => 'Boyka Ssj #'.random_int(1, 10),
                    'email' => 'a@yuriasthar.com',
                    'password' => Hash::make('password'),
                ],
                [
                    'name' => 'Boyka Ssj #'.random_int(1, 10),
                    'email' => 'b@yuriasthar.com',
                    'password' => Hash::make('password'),
                ],
            ])
            ->each(function (User $user) {
                TerminalTransport::get()
                    ->each(fn (TerminalTransport $terminalTransport) => TravelRequest::factory()
                        ->for($user)
                        ->for($terminalTransport)
                        ->createMany([
                            ['status' => Status::REQUESTED],
                            ['status' => Status::APPROVED],
                            ['status' => Status::CANCELLED],
                        ])
                        ->each(function (TravelRequest $travelRequest) use ($terminalTransport) {
                            $transport = $terminalTransport->transport;
                            $hotel = $transport->hotels()->first();

                            $hotel->hotelRooms()
                                ->inRandomOrder()
                                ->limit(1)
                                ->get()
                                ->each(fn (HotelRoom $hotelRoom) => $travelRequest->hotelRooms()->attach($hotelRoom));
                        }),
                    );
            });
    }
}
