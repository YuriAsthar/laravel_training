<?php

namespace App\Listeners\TravelRequest\Updated;

use App\Events\TravelRequest\Updated;
use App\Notifications\TravelRequest\TravelRequestStatusUpdatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use LaravelTraining\Enums\Models\TravelRequest\Status;
use Psr\Log\LoggerInterface;

class SendStatusUpdatedNotification implements ShouldQueue
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function shouldQueue(Updated $event): bool
    {
        return $event->travelRequest->wasChanged('status')
            && Status::canSendNotification($event->travelRequest->status->value);
    }

    public function handle(Updated $event): void
    {
        $this->logger->info('Send Notification: Starting listener.', [
            'travel_request_id' => $event->travelRequest->id,
        ]);

        $event->travelRequest->user->notify(new TravelRequestStatusUpdatedNotification($event->travelRequest));

        $this->logger->info('Send Notification: Finish listener.', [
            'travel_request_id' => $event->travelRequest->id,
        ]);
    }
}
