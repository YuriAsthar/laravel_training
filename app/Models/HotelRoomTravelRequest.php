<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class HotelRoomTravelRequest extends Pivot
{
    protected $table = 'hotel_room_travel_request';

    protected $fillable = [
        'id',
        'hotel_room_id',
        'travel_request_id',
        'created_at',
    ];

    public function hotelRoom(): BelongsTo
    {
        return $this->belongsTo(HotelRoom::class);
    }

    public function travelRequest(): BelongsTo
    {
        return $this->belongsTo(TravelRequest::class);
    }
}
