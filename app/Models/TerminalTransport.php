<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TerminalTransport extends Pivot
{
    use HasFactory;

    protected $table = 'terminal_transport';

    protected $fillable = [
        'id',
        'terminal_id',
        'transport_id',
        'created_at',
    ];

    public function terminal(): BelongsTo
    {
        return $this->belongsTo(Terminal::class);
    }

    public function transport(): BelongsTo
    {
        return $this->belongsTo(Transport::class);
    }
}
