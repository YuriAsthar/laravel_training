<?php

namespace App\Notifications\TravelRequest;

use App\Models\TravelRequest;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use LaravelTraining\Enums\Models\TravelRequest\Status;

class TravelRequestStatusUpdatedNotification extends Notification
{
    public function __construct(private readonly TravelRequest $travelRequest)
    {
    }

    public function via(): array
    {
        return ['mail'];
    }

    public function toMail(): MailMessage
    {
        $message = (new MailMessage())
            ->subject(__('notifications.travel_request.subject', [
                'status' => __('fields.'.$this->travelRequest->status->value),
            ]))
            ->greeting(__('notifications.travel_request.greeting', ['user_name' => $this->travelRequest->user->name]));

        return $this->buildDetailsMessage($this->travelRequest, $message);
    }

    private function buildDetailsMessage(TravelRequest $travelRequest, MailMessage $message): MailMessage
    {
        if ($travelRequest->status === Status::APPROVED) {
            return $message->line(__('notifications.travel_request.approved_body'));
        }

        return $message->line(__('notifications.travel_request.cancelled_body'));
    }
}
