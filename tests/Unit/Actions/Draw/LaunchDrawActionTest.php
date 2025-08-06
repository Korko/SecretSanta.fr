<?php

use App\Actions\Draw\LaunchDrawAction;
use App\Models\Draw\Draw;
use App\Models\Draw\Participant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

describe('LaunchDrawAction', function () {
    beforeEach(function () {
        Queue::fake();
        $this->action = new LaunchDrawAction();

        $this->draw = Draw::factory()->create(['status' => 'closed_registration']);

        Participant::factory()->count(5)->create([
            'draw_id' => $this->draw->id,
            'status' => 'accepted',
        ]);
    });

    test('launches draw successfully', function () {
        $result = $this->action->execute($this->draw);

        expect($result['success'])->toBeTrue();
        expect($result['message'])->toBe('Draw processing started');

        Queue::assertPushed(\App\Jobs\ProcessDrawJob::class, function ($job) {
            return true;
        });
    });

    test('prevents launch with insufficient participants', function () {
        $this->draw->acceptedParticipants()->skip(2)->take(3)->delete();

        $result = $this->action->execute($this->draw);

        expect($result['success'])->toBeFalse();
        expect($result['error'])->toContain('At least 3 participants');

        Queue::assertNotPushed(\App\Jobs\ProcessDrawJob::class);
    });

    test('prevents launch with wrong status', function () {
        $this->draw->update(['status' => 'draft']);

        $result = $this->action->execute($this->draw);

        expect($result['success'])->toBeFalse();
        expect($result['error'])->toContain('closed_registration state');
    });
});
