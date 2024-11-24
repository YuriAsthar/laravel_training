<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Resources\User as UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserController
{
    public function index(Request $request): JsonResource
    {
        return UserResource::collection(User::query()->simplePaginate($request->input('per_page', 10)));
    }
}
