<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Spatie\QueryBuilder\Filters\Filter;

class FilterByTerminalType implements Filter
{
    public function __invoke(Builder $query, $value, string $property): void
    {
        $query->whereHas(
            'terminalTransport',
            fn (Builder $query) => $query->whereHas(
                'terminal',
                fn (Builder $query) => $query->whereIn('type', Arr::wrap($value)),
            ),
        );
    }
}
