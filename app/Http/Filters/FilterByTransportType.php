<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Spatie\QueryBuilder\Filters\Filter;

class FilterByTransportType implements Filter
{
    public function __invoke(Builder $query, $value, string $property): void
    {
        $query->whereHas(
            'terminalTransport',
            fn (Builder $query) => $query->whereHas(
                'transport',
                fn (Builder $query) => $query->whereIn('type', Arr::wrap($value)),
            ),
        );
    }
}
