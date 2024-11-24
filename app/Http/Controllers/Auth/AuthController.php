<?php

namespace App\Http\Controllers\Auth;

use App\Resources\Jwt as JwtResource;
use App\Resources\User as UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AuthController
{
    public function __construct()
    {
    }

    public function login(): JwtResource|JsonResponse
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(status: Response::HTTP_UNAUTHORIZED);
        }

        return JwtResource::make(['token' => $token]);
    }

    public function me(): UserResource
    {
        return UserResource::make(auth()->user());
    }

    public function logout(): JsonResponse
    {
        auth()->logout(true);

        return response()->json(status: Response::HTTP_NO_CONTENT);
    }

    public function refresh(): JwtResource
    {
        return JwtResource::make(auth()->refresh());
    }
}
