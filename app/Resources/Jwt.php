<?php

namespace App\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Jwt extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'access_token' => $this->token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * config('jwt.ttl'),
        ];
    }
}
