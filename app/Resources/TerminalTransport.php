<?php

namespace App\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TerminalTransport extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'terminal_id' => $this->terminal_id,
            'transport_id' => $this->transport_id,
            'created_at' => $this->created_at->toIso8601String(),
            'terminal' => Terminal::make($this->whenLoaded('terminal')),
            'transport' => Transport::make($this->whenLoaded('transport')),
        ];
    }
}
