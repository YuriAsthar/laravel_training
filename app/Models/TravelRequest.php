<?php

namespace App\Models;

use App\Events\TravelRequest\Updated;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
        'id',
        'status',
        'user_id',
        'terminal_transport_id',
        'created_at',
        'deleted_at',
        'start_date',
        'end_date',
    ];

    protected $dispatchesEvents = [
        'updated' => Updated::class,
    ];

    protected function casts(): array
    {
        return [
            'status' => Status::class,
            'deleted_at' => 'datetime',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
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

    public function amount(): Attribute
    {
        return Attribute::get(
            fn () => $this->hotelRooms->sum('amount'),
        );
    }

    public function prunable(): Builder
    {
        return static::where('deleted_at', '<=', now()->subDays(30));
    }
}
