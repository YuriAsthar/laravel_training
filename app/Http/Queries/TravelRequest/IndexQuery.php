<?php

namespace App\Http\Queries\TravelRequest;

use App\Http\Filters\FilterByHotelName;
use App\Http\Filters\FilterByTerminalType;
use App\Http\Filters\FilterByTransportType;
use App\Http\Filters\FilterDateGreaterThan;
use App\Http\Filters\FilterDateLessThan;
use App\Models\TravelRequest;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class IndexQuery extends QueryBuilder
{
    public function __construct()
    {
        parent::__construct(TravelRequest::query());

        $this->allowedFilters([
            AllowedFilter::exact('status'),
            AllowedFilter::custom('hotel_name', new FilterByHotelName()),
            AllowedFilter::custom('terminal_type', new FilterByTerminalType()),
            AllowedFilter::custom('transport_type', new FilterByTransportType()),
            AllowedFilter::custom('start_date_greater_than', new FilterDateGreaterThan(), 'start_date'),
            AllowedFilter::custom('start_date_less_than', new FilterDateLessThan(), 'start_date'),
            AllowedFilter::custom('end_date_greater_than', new FilterDateGreaterThan(), 'end_date'),
            AllowedFilter::custom('end_date_less_than', new FilterDateLessThan(), 'end_date'),
            AllowedFilter::custom('created_at_greater_than', new FilterDateGreaterThan(), 'created_at'),
            AllowedFilter::custom('created_at_less_than', new FilterDateLessThan(), 'created_at'),
        ]);

        $this->allowedIncludes(['user', 'terminalTransport.terminal', 'terminalTransport.transport', 'hotelRooms.hotel']);

        $this->defaultSort('-id');

        $this->allowedSorts(['id']);
    }
}
