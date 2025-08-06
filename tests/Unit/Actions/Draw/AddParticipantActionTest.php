<?php

use App\Actions\Draw\AddParticipantAction;
use App\Actions\Draw\CreateDrawAction;
use App\Managers\Encryption\SecretSantaEncryptionManager;
use App\Models\Draw\Draw;
use App\Models\Draw\Participant;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('AddParticipantAction', function () {
    beforeEach(function () {
        $encryptionManager = app(SecretSantaEncryptionManager::class);
        $this->action = new AddParticipantAction($encryptionManager);

        $createAction = new CreateDrawAction($encryptionManager);
        $result = $createAction->execute([
            'title' => 'Test Draw',
            'organizer_name' => 'Organisateur',
            'organizer_email' => 'org@example.com',
        ]);

        $this->draw = $result['draw'];
        $this->masterKey = $result['master_key'];
    });

    test('adds participant successfully', function () {
        $data = [
            'name' => 'Alice',
            'email' => 'alice@example.com',
        ];

        $result = $this->action->execute($this->draw, $data, $this->masterKey);

        expect($result['success'])->toBeTrue();
        expect($result['participant'])->toBeInstanceOf(Participant::class);
        expect($result['participant_link'])->not->toBeEmpty();

        $this->assertDatabaseHas('participants', [
            'draw_id' => $this->draw->id,
            'uuid' => $result['participant']->uuid,
            'is_organizer' => false,
        ]);
    });

    test('auto accepts participant when enabled', function () {
        $this->draw->update(['auto_accept_participants' => true]);

        $data = [
            'name' => 'Bob',
            'email' => 'bob@example.com',
        ];

        $result = $this->action->execute($this->draw, $data, $this->masterKey);

        expect($result['success'])->toBeTrue();
        expect($result['participant']->status)->toBe('accepted');
        expect($result['participant']->accepted_at)->not->toBeNull();
    });

    test('prevents duplicate names', function () {
        $this->action->execute($this->draw, [
            'name' => 'Charlie',
            'email' => 'charlie1@example.com',
        ], $this->masterKey);

        $result = $this->action->execute($this->draw, [
            'name' => 'Charlie',
            'email' => 'charlie2@example.com',
        ], $this->masterKey);

        expect($result['success'])->toBeFalse();
        expect($result['error'])->toContain('already exists');
    });
});
