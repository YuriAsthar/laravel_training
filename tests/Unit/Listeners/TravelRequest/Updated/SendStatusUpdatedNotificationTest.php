<?php

namespace Tests\Unit\Listeners\TravelRequest\Updated;

use App\Events\TravelRequest\Updated;
use App\Listeners\TravelRequest\Updated\SendStatusUpdatedNotification;
use App\Models\TravelRequest;
use App\Models\User;
use App\Notifications\TravelRequest\TravelRequestStatusUpdatedNotification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use LaravelTraining\Enums\Models\TravelRequest\Status;
use Psr\Log\NullLogger;
use Tests\TestCase;

class SendStatusUpdatedNotificationTest extends TestCase
{
    private User $user;

    private TravelRequest $travelRequest;

    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();
        Notification::fake();

        $this->user = User::factory()->create();
        $this->travelRequest = TravelRequest::factory()->for($this->user)->create();
    }

    public function test_if_should_queue_return_true_properly_when_new_status_is_cancelled(): void
    {
        $sendStatusUpdatedNotificationListener = $this->sendStatusUpdatedNotificationListener();

        $this->travelRequest->update(['status' => Status::CANCELLED]);

        $this->assertTrue($sendStatusUpdatedNotificationListener->shouldQueue($this->event($this->travelRequest)));
    }

    public function test_if_should_queue_return_true_properly_when_new_status_is_approved(): void
    {
        $sendStatusUpdatedNotificationListener = $this->sendStatusUpdatedNotificationListener();

        $this->travelRequest->update(['status' => Status::APPROVED]);

        $this->assertTrue($sendStatusUpdatedNotificationListener->shouldQueue($this->event($this->travelRequest)));
    }

    public function test_if_should_queue_return_true_properly_when_new_status_is_requested(): void
    {
        $sendStatusUpdatedNotificationListener = $this->sendStatusUpdatedNotificationListener();

        $this->travelRequest->update(['status' => Status::REQUESTED]);

        $this->assertFalse($sendStatusUpdatedNotificationListener->shouldQueue($this->event($this->travelRequest)));
    }

    public function test_if_sending_mail_properly_with_approved_status(): void
    {
        $sendStatusUpdatedNotificationListener = $this->sendStatusUpdatedNotificationListener();
        $event = $this->event($this->travelRequest);

        $this->travelRequest->update(['status' => Status::APPROVED]);

        $this->assertTrue($sendStatusUpdatedNotificationListener->shouldQueue($event));

        $sendStatusUpdatedNotificationListener->handle($event);

        Notification::assertSentTo($this->user, TravelRequestStatusUpdatedNotification::class, function ($notification) {
            $mail = $notification->toMail($this->user);

            $this->assertEquals('Solicitação de viagem aprovado!', $mail->subject);
            $this->assertEquals("Olá, {$this->user->name}!", $mail->greeting);
            $this->assertEquals('Sua solicitação de viagem foi **aprovada** com sucesso!', $mail->introLines[0]);

            return true;
        });
    }

    public function test_if_sending_mail_properly_with_cancelled_status(): void
    {
        $sendStatusUpdatedNotificationListener = $this->sendStatusUpdatedNotificationListener();
        $event = $this->event($this->travelRequest);

        $this->travelRequest->update(['status' => Status::CANCELLED]);

        $this->assertTrue($sendStatusUpdatedNotificationListener->shouldQueue($event));

        $sendStatusUpdatedNotificationListener->handle($event);

        Notification::assertSentTo($this->user, TravelRequestStatusUpdatedNotification::class, function ($notification) {
            $mail = $notification->toMail($this->user);

            $this->assertEquals('Solicitação de viagem cancelado!', $mail->subject);
            $this->assertEquals("Olá, {$this->user->name}!", $mail->greeting);
            $this->assertEquals('Infelizmente sua solicitação de viagem foi **cancelada**.', $mail->introLines[0]);

            return true;
        });
    }

    private function sendStatusUpdatedNotificationListener(): SendStatusUpdatedNotification
    {
        return new SendStatusUpdatedNotification(app(NullLogger::class));
    }

    private function event(TravelRequest $travelRequest): Updated
    {
        return new Updated($travelRequest);
    }
}
