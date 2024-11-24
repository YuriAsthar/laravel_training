<?php

namespace App\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HotelRoom extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'room_number' => $this->room_number,
            'floor_number' => $this->floor_number,
            'bed_quantity' => $this->bed_quantity,
            'amount' => $this->amount,
            'currency' => $this->currency->value,
            'hotel_id' => $this->hotel_id,
            'created_at' => $this->created_at->toIso8601String(),
            'deleted_at' => $this->deleted_at?->toIso8601String(),
            'hotel' => Hotel::make($this->whenLoaded('hotel')),
        ];
    }
}
