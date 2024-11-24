<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hotel extends Model
{
    use HasFactory,
        MassPrunable,
        SoftDeletes;

    protected $table = 'hotels';

    protected $fillable = [
        'id',
        'name',
        'country',
        'city',
        'state',
        'neighborhood',
        'street',
        'number',
        'created_at',
        'deleted_at',
    ];

    protected function casts(): array
    {
        return [
            'deleted_at' => 'datetime',
        ];
    }

    public function hotelRooms(): HasMany
    {
        return $this->hasMany(HotelRoom::class);
    }

    public function prunable(): Builder
    {
        return static::where('deleted_at', '<=', now()->subDays(30));
    }
}
