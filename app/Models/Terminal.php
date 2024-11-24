<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use LaravelTraining\Enums\Models\Terminal\Type;

class Terminal extends Model
{
    use HasFactory,
        MassPrunable,
        SoftDeletes;

    protected $table = 'terminals';

    protected $fillable = [
        'id',
        'type',
        'name',
        'country',
        'city',
        'state',
        'neighborhood',
        'street',
        'street_number',
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

    public function transports(): BelongsToMany
    {
        return $this->belongsToMany(Transport::class)
            ->using(TerminalTransport::class)
            ->withTimestamps();
    }

    public function terminalTransport(): HasMany
    {
        return $this->hasMany(TerminalTransport::class);
    }

    public function travelRequests(): HasMany
    {
        return $this->hasMany(TravelRequest::class);
    }

    public function prunable(): Builder
    {
        return static::where('deleted_at', '<=', now()->subDays(30));
    }
}
