<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class HotelTransport extends Pivot
{
    use HasFactory;

    protected $table = 'hotel_transport';

    protected $fillable = [
        'id',
        'hotel_id',
        'transport_id',
        'created_at',
    ];

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function transport(): BelongsTo
    {
        return $this->belongsTo(Transport::class);
    }
}
