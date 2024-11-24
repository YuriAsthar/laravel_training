<?php

namespace App\Http\Controllers\TravelRequest;

use App\Http\Queries\TravelRequest\IndexQuery;
use App\Http\Requests\TravelRequest\IndexRequest;
use App\Http\Requests\TravelRequest\StoreRequest;
use App\Http\Requests\TravelRequest\UpdateRequest;
use App\Models\TravelRequest;
use App\Resources\TravelRequest as TravelRequestResource;
use Illuminate\Contracts\Auth\Factory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TravelRequestController
{
    use AuthorizesRequests;

    public function __construct(private readonly Factory $auth)
    {
    }

    public function index(IndexRequest $request, IndexQuery $query): JsonResource
    {
        $user = $this->auth->user();

        $this->authorize('viewAny', [TravelRequest::class]);

        return TravelRequestResource::collection(
            $query->whereBelongsTo($user)->simplePaginate(
                $request->input('per_page', 10),
            ),
        );
    }

    public function show(IndexRequest $travelRequest, IndexQuery $query): TravelRequestResource
    {
        $user = $this->auth->user();

        $this->authorize('view', [TravelRequest::class, $travelRequest]);

        return TravelRequestResource::make(
            $query->whereBelongsTo($user)->where('id', $travelRequest->id)->first(),
        );
    }

    public function store(StoreRequest $request): TravelRequestResource
    {
        $user = $this->auth->user();
        $input = $request->validated();
        $travelRequest = $user->travelRequests()->create($input);

        $travelRequest->hotelRooms()->attach($input['hotel_room_ids']);

        return TravelRequestResource::make($travelRequest->load('hotelRooms'));
    }

    public function update(IndexQuery $query, TravelRequest $travelRequest, UpdateRequest $request): TravelRequestResource
    {
        $user = $this->auth->user();

        $this->authorize('viewAny', [TravelRequest::class, $user]);

        $travelRequest->update($request->validated());

        return TravelRequestResource::make($travelRequest);
    }
}
