<?php

namespace App\Http\Requests\TravelRequest;

use App\Models\HotelRoom;
use App\Models\TerminalTransport;
use App\Models\TravelRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use LaravelTraining\Enums\Models\TravelRequest\Status;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', [TravelRequest::class]);
    }

    public function rules(): array
    {
        return [
            'terminal_transport_id' => [
                'required',
                'integer',
            ],
            'hotel_room_ids' => [
                'present',
            ],
            'hotel_room_ids.*' => [
                'sometimes',
                'integer',
                Rule::exists('hotel_rooms', 'id')
                    ->whereNull('deleted_at'),
            ],
        ];
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated();
        $data['status'] = Status::REQUESTED();

        if (! array_key_exists('hotel_room_ids', $data)) {
            $data['hotel_room_ids'] = [];
        }

        return data_get($data, $key, $default);
    }

    public function withValidator(Validator $validator): void
    {
        if ($validator->fails()) {
            return;
        }

        $validator->after(function (Validator $validator) {
            if ($this->userAlreadyHasSameTravelRequest()) {
                $validator->errors()->add(
                    'terminal_transport_id',
                    __('validation.custom.travel_request.invalid_terminal_transport_id'),
                );
            }

            if (! $this->terminalTransportExists()) {
                $validator->errors()->add(
                    'terminal_transport_id',
                    __('validation.custom.travel_request.not_exist_terminal_transport_id'),
                );
            }
        });
    }

    private function userAlreadyHasSameTravelRequest(): bool
    {
        return TravelRequest::where('user_id', auth()->user()->id)
            ->where('terminal_transport_id', $this->input('terminal_transport_id'))
            ->where('status', Status::REQUESTED)
            ->exists();
    }

    private function terminalTransportExists(): bool
    {
        return TerminalTransport::where('id', $this->input('terminal_transport_id'))
            ->exists();
    }
}
