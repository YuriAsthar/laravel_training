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
use LaravelTraining\Enums\Models\TravelRequest\Status;

class TravelRequest extends Model
{
    use MassPrunable,
        HasFactory,
        SoftDeletes;

    protected $table = 'travel_requests';

    protected $fillable = [
        'status',
        'user_id',
        'transport_terminal_id',
        'created_at',
        'deleted_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => Status::class,
            'deleted_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hotelRoomTravelRequest(): HasMany
    {
        return $this->hasMany(HotelRoomTravelRequest::class);
    }

    public function hotelRooms(): BelongsToMany
    {
        return $this->belongsToMany(HotelRoom::class)
            ->using(HotelRoomTravelRequest::class)
            ->withTimestamps();
    }

    public function terminalTransport(): BelongsTo
    {
        return $this->belongsTo(TerminalTransport::class);
    }

    public function prunable(): Builder
    {
        return static::where('deleted_at', '<=', now()->subDays(30));
    }
}
