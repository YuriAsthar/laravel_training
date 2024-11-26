<?php

namespace App\Listeners\Hotel;

use App\Models\Hotel;
use App\Resources\Hotel as HotelResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HotelController
{
    public function __construct(private readonly Hotel $hotel)
    {
    }

    public function index(Request $request): JsonResource
    {
        return HotelResource::collection($this->hotel->latest()->with('hotelRooms')->simplePaginate(
            $request->input('per_page', 10),
        ));
    }
}
