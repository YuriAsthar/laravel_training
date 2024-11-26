<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use LaravelTraining\Enums\Models\HotelRoom\Currency;

class HotelRoom extends Model
{
    use HasFactory,
        MassPrunable,
        SoftDeletes;

    protected $table = 'hotel_rooms';

    protected $fillable = [
        'id',
        'room_number',
        'floor_number',
        'bed_quantity',
        'amount',
        'currency',
        'hotel_id',
        'created_at',
        'deleted_at',
    ];

    protected function casts(): array
    {
        return [
            'deleted_at' => 'datetime',
            'currency' => Currency::class,
            'amount' => 'int',
            'floor_number' => 'string',
            'room_number' => 'string',
            'bed_quantity' => 'string',
        ];
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function hotelRoomTravelRequest(): HasMany
    {
        return $this->hasMany(HotelRoomTravelRequest::class);
    }

    public function travelRequests(): BelongsToMany
    {
        return $this->belongsToMany(TravelRequest::class)
            ->using(HotelRoomTravelRequest::class)
            ->withTimestamps();
    }

    public function prunable(): Builder
    {
        return static::where('deleted_at', '<=', now()->subDays(30));
    }
}
