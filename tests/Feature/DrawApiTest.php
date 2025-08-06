<?php

use App\Managers\Encryption\SecretSantaEncryptionManager;
use App\Models\Draw\Draw;
use function Pest\Laravel\{postJson, getJson, patchJson, actingAs};

describe('Draw API', function () {

    test('creates a draw successfully', function () {
        $response = postJson('/api/v1/draws', [
            'title' => 'Secret Santa 2024',
            'description' => 'Tirage de Noël',
            'organizer_name' => 'Jean Dupont',
            'organizer_email' => 'jean@example.com',
            'auto_accept_participants' => true,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'draw' => ['uuid', 'status'],
                'organizer_link',
                'master_key',
            ]);

        $this->assertDatabaseHas('draws', [
            'uuid' => $response->json('draw.uuid'),
            'status' => 'draft',
        ]);
    });

    test('requires master key to add participant', function () {
        $draw = createDraw();

        $response = postJson("/api/v1/draws/{$draw->uuid}/participants", [
            'name' => 'Alice',
            'email' => 'alice@example.com',
        ]);

        $response->assertStatus(401)
            ->assertJson(['error' => 'Master key required']);
    });

    test('adds participant with valid master key', function () {
        $encryptionManager = app(SecretSantaEncryptionManager::class);
        $encryption = $encryptionManager->createDrawEncryption();

        $draw = Draw::factory()->create([
            'organizer_key_hash' => $encryption['organizer_key_hash'],
            'master_key_encrypted' => $encryption['master_key_encrypted'],
        ]);

        $response = postJson(
            "/api/v1/draws/{$draw->uuid}/participants",
            [
                'name' => 'Alice',
                'email' => 'alice@example.com',
            ],
            ['X-Master-Key' => base64_encode($encryption['master_key'])]
        );

        $response->assertStatus(201)
            ->assertJsonStructure([
                'participant' => ['uuid', 'status'],
                'participant_link',
            ]);
    });

    test('launches draw with sufficient participants', function () {
        $draw = createDraw(['status' => 'closed_registration']);

        // Créer 5 participants
        foreach (range(1, 5) as $i) {
            createParticipant($draw);
        }

        $response = postJson("/api/v1/draws/{$draw->uuid}/launch");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Draw processing started']);

        // Vérifier que le job est dispatché
        \Illuminate\Support\Facades\Queue::assertPushed(\App\Jobs\ProcessDrawJob::class);
    });

    test('prevents launch with insufficient participants', function () {
        $draw = createDraw(['status' => 'closed_registration']);
        createParticipant($draw);
        createParticipant($draw);

        $response = postJson("/api/v1/draws/{$draw->uuid}/launch");

        $response->assertStatus(422)
            ->assertJsonFragment(['error' => 'At least 3 participants are required (found: 2)']);
    });
});
