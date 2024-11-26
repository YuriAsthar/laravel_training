<?php

namespace Feature\Http\Controllers\TerminalTransport;

use App\Models\Terminal;
use App\Models\Transport;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class TerminalTransportControllerTest extends TestCase
{
    private User $user;

    private array $headers;

    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();

        $this->user = User::factory()->create();
        $this->headers = ['Authorization' => 'Bearer '.$this->generateJwtToken($this->user)];
    }

    public function test_index_endpoint_properly(): void
    {
        $terminalOne = Terminal::factory()->create();
        $transportOne = Transport::factory()->create();
        $terminalTwo = Terminal::factory()->create();
        $transportTwo = Transport::factory()->create();

        $terminalOne->transports()->attach($transportOne);
        $terminalTwo->transports()->attach($transportTwo);

        $this->getJson(
            route('api.internal.terminal-transport.index', $this->headers)
        )
            ->assertJsonPath('data.0.transport.id', $transportOne->id)
            ->assertJsonPath('data.0.transport.type', $transportOne->type->value)
            ->assertJsonPath('data.0.terminal.id', $terminalOne->id)
            ->assertJsonPath('data.0.terminal.type', $terminalOne->type->value)
            ->assertJsonPath('data.1.transport.id', $transportTwo->id)
            ->assertJsonPath('data.1.transport.type', $transportTwo->type->value)
            ->assertJsonPath('data.1.terminal.id', $terminalTwo->id)
            ->assertJsonPath('data.1.terminal.type', $terminalTwo->type->value)
            ->assertSuccessful();
    }
}
