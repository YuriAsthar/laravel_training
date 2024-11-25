<?php

namespace App\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Hotel extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'country' => $this->country,
            'city' => $this->city,
            'state' => $this->state,
            'neighborhood' => $this->neighborhood,
            'street' => $this->street,
            'number' => $this->number,
            'created_at' => $this->created_at->toIso8601String(),
            'deleted_at' => $this->deleted_at?->toIso8601String(),
        ];
    }
}
