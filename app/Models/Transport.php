<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use LaravelTraining\Enums\Models\Transport\Type;

class Transport extends Model
{
    use HasFactory,
        MassPrunable,
        SoftDeletes;

    protected $table = 'transports';

    protected $fillable = [
        'id',
        'type',
        'name',
        'created_at',
        'deleted_at',
    ];

    protected function casts(): array
    {
        return [
            'deleted_at' => 'datetime',
            'type' => Type::class,
        ];
    }

    public function hotelTransport(): HasMany
    {
        return $this->hasMany(HotelTransport::class);
    }

    public function hotels(): BelongsToMany
    {
        return $this->belongsToMany(Hotel::class)
            ->using(HotelTransport::class)
            ->withTimestamps();
    }

    public function terminalTransport(): HasMany
    {
        return $this->hasMany(TerminalTransport::class);
    }

    public function terminals(): BelongsToMany
    {
        return $this->belongsToMany(Terminal::class)
            ->using(TerminalTransport::class)
            ->withTimestamps();
    }

    public function prunable(): Builder
    {
        return static::where('deleted_at', '<=', now()->subDays(30));
    }
}
