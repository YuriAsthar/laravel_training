<?php

namespace App\Http\Requests\TravelRequest;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Throwable;

class IndexRequest extends FormRequest
{
    private const START_DATE_GREATER_THAN = 'start_date_greater_than';

    private const START_DATE_LESS_THAN = 'start_date_less_than';

    private const END_DATE_GREATER_THAN = 'end_date_greater_than';

    private const END_DATE_LESS_THAN = 'end_date_less_than';

    private const CREATED_AT_GREATER_THAN = 'created_at_greater_than';

    private const CREATED_AT_LESS_THAN = 'created_at_less_than';

    public function rules(): array
    {
        return [
            'filter.start_date_greater_than' => ['sometimes', 'integer'],
            'filter.start_date_less_than' => ['sometimes', 'integer'],
            'filter.end_date_greater_than' => ['sometimes', 'integer'],
            'filter.end_date_less_than' => ['sometimes', 'integer'],
            'filter.created_at_greater_than' => ['sometimes', 'integer'],
            'filter.created_at_less_than' => ['sometimes', 'integer'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            foreach ($this->input('filter', []) as $filterName => $filterValue) {
                if (in_array($filterName, $this->filtersToValidate())) {
                    try {
                        Carbon::createFromTimestamp(Arr::wrap($filterValue)[0]);
                    } catch (Throwable) {
                        $validator->errors()->add(
                            'filter.'.$filterName,
                            __('validation.custom.filter.need_timestamp_format', [
                                'filter' => $filterName,
                            ]),
                        );
                    }
                }
            }
        });
    }

    private function filtersToValidate(): array
    {
        return [
            self::START_DATE_GREATER_THAN,
            self::START_DATE_LESS_THAN,
            self::END_DATE_GREATER_THAN,
            self::END_DATE_LESS_THAN,
            self::CREATED_AT_GREATER_THAN,
            self::CREATED_AT_LESS_THAN,
        ];
    }
}
