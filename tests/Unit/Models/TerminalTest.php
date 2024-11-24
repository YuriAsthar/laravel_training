<?php

namespace Tests\Unit\Models;

use App\Models\Terminal;
use App\Models\Transport;
use App\Models\User;
use Illuminate\Database\Console\PruneCommand;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class TerminalTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    private Transport $transport;

    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();

        $this->user = User::factory()->create();
        $this->transport = Transport::factory()->create();
    }

    public function test_if_generate_model_factory_properly(): void
    {
        $this->freezeSecond();

        $terminal = Terminal::factory()->create();

        $this->assertDatabaseHas('terminals', [
            'id' => $terminal->id,
        ]);
    }

    public function test_if_model_relations_properly(): void
    {
        $this->freezeSecond();

        $terminal = Terminal::factory()->create();

        $terminal->transports()->attach($this->transport);

        $this->assertEquals($this->transport->fresh(), $terminal->transports()->first()->fresh());

        $this->assertDatabaseHas('terminal_transport', [
            'transport_id' => $this->transport->id,
            'terminal_id' => $terminal->id,
        ]);
    }

    public function test_if_it_will_prune_properly(): void
    {
        $this->freezeTime();

        $terminalOne = Terminal::factory()->create(['deleted_at' => now()]);
        $terminalTwo = Terminal::factory()->create(['deleted_at' => now()->addDays(29)]);
        $terminalThree = Terminal::factory()->create(['deleted_at' => now()->subDays(30)]);
        $terminalFour = Terminal::factory()->create(['deleted_at' => now()->subDays(90)]);

        $this->artisan(PruneCommand::class, [
            '--model' => Terminal::class,
        ])
            ->assertSuccessful();

        $this->assertDatabasehas('terminals', ['id' => $terminalOne->id]);
        $this->assertDatabasehas('terminals', ['id' => $terminalTwo->id]);
        $this->assertDatabaseMissing('terminals', ['id' => $terminalThree->id]);
        $this->assertDatabaseMissing('terminals', ['id' => $terminalFour->id]);
    }

    public function test_if_soft_deleted_model_properly(): void
    {
        $this->freezeTime();

        $terminal = Terminal::factory()->create();

        $terminal->delete();

        $this->assertDatabaseHas('terminals', [
            'id' => $terminal->id,
            'deleted_at' => now(),
        ]);
    }
}
