<?php

namespace App\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TravelRequest extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'status' => $this->status->value,
            'amount' => $this->amount,
            'user_id' => $this->user_id,
            'terminal_transport_id' => $this->terminal_transport_id,
            'start_date' => $this->start_date->toIso8601String(),
            'end_date' => $this->end_date->toIso8601String(),
            'created_at' => $this->created_at->toIso8601String(),
            'deleted_at' => $this->deleted_at?->toIso8601String(),
            'user' => User::make($this->whenLoaded('user')),
            'terminal_transport' => TerminalTransport::make($this->whenLoaded('terminalTransport')),
            'hotel_rooms' => HotelRoom::collection($this->whenLoaded('hotelRooms')),
        ];
    }
}
