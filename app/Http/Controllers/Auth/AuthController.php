<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\LoginRequest;
use App\Resources\Jwt as JwtResource;
use App\Resources\User as UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AuthController
{
    public function __construct()
    {
    }

    public function login(LoginRequest $request): JwtResource|JsonResponse
    {
        $credentials = $request->validated();

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(status: Response::HTTP_UNAUTHORIZED);
        }

        return JwtResource::make((object) ['token' => $token])->response()->setStatusCode(Response::HTTP_CREATED);
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
        return JwtResource::make((object) ['token' => auth()->refresh()]);
    }
}
