<?php

namespace App\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Transport extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'name' => $this->name,
            'created_at' => $this->created_at->toIso8601String(),
            'deleted_at' => $this->deleted_at?->toIso8601String(),
        ];
    }
}
