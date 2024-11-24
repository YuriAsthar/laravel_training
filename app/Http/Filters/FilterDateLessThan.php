<?php

namespace App\Http\Filters;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Spatie\QueryBuilder\Filters\Filter;

class FilterDateLessThan implements Filter
{
    public function __invoke(Builder $query, $value, string $property): void
    {
        $query->where($property, '<=', Carbon::createFromTimestamp(Arr::wrap($value)[0]));
    }
}
