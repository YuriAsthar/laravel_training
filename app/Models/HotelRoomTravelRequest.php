<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class HotelRoomTravelRequest extends Pivot
{
    use HasFactory,
        MassPrunable,
        SoftDeletes;

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
