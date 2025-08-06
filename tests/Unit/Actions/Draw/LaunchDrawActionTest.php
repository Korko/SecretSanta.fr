<?php

namespace Tests\Unit\Actions\Draw;

use App\Actions\Draw\LaunchDrawAction;
use App\Models\Draw\Draw;
use App\Models\Draw\Participant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class LaunchDrawActionTest extends TestCase
{
    use RefreshDatabase;

    private LaunchDrawAction $action;
    private Draw $draw;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
        $this->action = new LaunchDrawAction();

        // Créer un tirage avec des participants
        $this->draw = Draw::factory()->create(['status' => 'closed_registration']);

        // Créer 5 participants acceptés
        Participant::factory()->count(5)->create([
            'draw_id' => $this->draw->id,
            'status' => 'accepted',
        ]);
    }

    public function test_launches_draw_successfully()
    {
        $result = $this->action->execute($this->draw);

        $this->assertTrue($result['success']);
        $this->assertEquals('Draw processing started', $result['message']);

        Queue::assertPushed(\App\Jobs\ProcessDraw::class, function ($job) {
            return true;
        });
    }

    public function test_prevents_launch_with_insufficient_participants()
    {
        // Supprimer des participants pour n'en garder que 2
        $this->draw->acceptedParticipants()->skip(2)->take(3)->delete();

        $result = $this->action->execute($this->draw);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('At least 3 participants', $result['error']);

        Queue::assertNotPushed(\App\Jobs\ProcessDraw::class);
    }

    public function test_prevents_launch_with_wrong_status()
    {
        $this->draw->update(['status' => 'draft']);

        $result = $this->action->execute($this->draw);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('closed_registration state', $result['error']);
    }
}
