<?php

use App\Actions\Draw\CreateDrawAction;
use App\Managers\Encryption\SecretSantaEncryptionManager;
use App\Models\Draw\Draw;
use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('CreateDrawAction', function () {
    beforeEach(function () {
        $this->encryptionManager = app(SecretSantaEncryptionManager::class);
        $this->action = new CreateDrawAction($this->encryptionManager);
    });

    test('creates draw successfully', function () {
        $data = [
            'title' => 'Secret Santa 2024',
            'description' => 'Tirage pour Noël',
            'organizer_name' => 'Jean Dupont',
            'organizer_email' => 'jean@example.com',
            'auto_accept_participants' => true,
            'allow_target_messages' => true,
        ];

        $result = $this->action->execute($data);
        
        if (!$result['success']) {
            dump($result['error']);
        }

        expect($result['success'])->toBeTrue();
        expect($result['draw'])->toBeInstanceOf(Draw::class);
        expect($result['organizer_link'])->not->toBeEmpty();
        expect($result['master_key'])->not->toBeEmpty();

        $this->assertDatabaseHas('draws', [
            'uuid' => $result['draw']->uuid,
            'status' => 'draft'
        ]);

        $this->assertDatabaseHas('participants', [
            'draw_id' => $result['draw']->id,
            'is_organizer' => true,
            'status' => 'accepted'
        ]);
    });

    test('creates draw with user', function () {
        $user = User::factory()->create();

        $data = [
            'title' => 'Secret Santa 2024',
            'organizer_name' => 'Jean Dupont',
            'organizer_email' => 'jean@example.com',
        ];

        $result = $this->action->execute($data, $user);

        expect($result['success'])->toBeTrue();
        expect($result['draw']->user_id)->toBe($user->id);
    });

    test('handles creation failure', function () {
        $data = [
            'title' => '',
        ];

        $result = $this->action->execute($data);

        expect($result['success'])->toBeFalse();
        expect($result)->toHaveKey('error');
    });
});
