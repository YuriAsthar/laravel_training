<?php

namespace App\Http\Requests\TravelRequest;

use App\Models\TerminalTransport;
use App\Models\TravelRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use LaravelTraining\Enums\Models\TravelRequest\Status;
use const Grpc\STATUS_ABORTED;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', [TravelRequest::class, $this->route('travelRequest')]);
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                'string',
                Rule::in([Status::CANCELLED(), Status::APPROVED()]),
            ],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        if ($validator->fails()) {
            return;
        }

        $validator->after(function ($validator) {
            $travelRequestActualStatus = $this->route('travelRequest')->status;

            if (! $travelRequestActualStatus->canCancel()) {
                $validator->errors()->add(
                    'terminal_transport_id',
                    __('validation.custom.travel_request.cannot_cancel', [
                        'status' => __('fields.'.$travelRequestActualStatus->value),
                    ]),
                );
            }
        });
    }
}
