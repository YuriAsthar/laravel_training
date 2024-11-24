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
use Illuminate\Support\Str;
use LaravelTraining\Enums\Models\TravelRequest\Status;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Transport::factory(10)
            ->create()
            ->each(function (Transport $transport) {
                Terminal::factory(3)
                    ->create()
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
                    'email' => Str::uuid()->toString().'@yuriasthar.com',
                    'password' => Hash::make('password'),
                ],
                [
                    'name' => 'Boyka Ssj #'.random_int(1, 10),
                    'email' => Str::uuid()->toString().'@yuriasthar.com',
                    'password' => Hash::make('password'),
                ],
            ])
            ->each(function (User $user) {
                $terminalTransport = TerminalTransport::first();

                TravelRequest::factory()
                    ->for($user)
                    ->for($terminalTransport)
                    ->createMany([
                        ['status' => Status::REQUESTED],
                        ['status' => Status::APPROVED],
                        ['status' => Status::CANCELLED],
                    ])
                    ->each(function (TravelRequest $travelRequest) use ($terminalTransport) {
                        $hotel = $terminalTransport->transport->hotels()->first();

                        $travelRequest->hotelRooms()->attach($hotel->hotelRooms()->first());
                    });
            });
    }
}
