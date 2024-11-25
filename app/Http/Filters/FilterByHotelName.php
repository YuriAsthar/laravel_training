<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Spatie\QueryBuilder\Filters\Filter;

class FilterByHotelName implements Filter
{
    public function __invoke(Builder $query, $value, string $property): void
    {
        $query->whereHas(
            'hotelRooms.hotel',
            fn ($query) => $query->where(function (Builder $query) use ($value) {
                foreach (Arr::wrap($value) as $hotelName) {
                    $query->orWhere('name', 'ILIKE', '%'.$hotelName.'%');
                }
            }),
        );
    }
}
