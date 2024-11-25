<?php

namespace App\Providers;

use App\Events\TravelRequest\Updated;
use App\Listeners\TravelRequest\Updated\SendStatusUpdatedNotification;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event as EventFacades;

class EventServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerEvents();
    }

    private function registerEvents(): void
    {
        foreach ($this->listen() as $event => $listeners) {
            foreach ($listeners as $listener) {
                EventFacades::listen($event, $listener);
            }
        }
    }

    public function listen(): array
    {
        return [
            Updated::class => [
                SendStatusUpdatedNotification::class,
            ],
        ];
    }
}
