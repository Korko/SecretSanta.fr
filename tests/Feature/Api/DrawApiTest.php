<?php

use App\Models\Draw\Draw;
use App\Models\Draw\Participant;
use Illuminate\Support\Facades\Queue;
use function Pest\Laravel\{postJson, patchJson};

beforeEach(function () {
    Queue::fake();
    $this->apiPrefix = '/api/v1';
});

describe('Draw API', function () {
    
    test('creates a draw successfully', function () {
        $response = postJson("{$this->apiPrefix}/draws", [
            'title' => 'Secret Santa 2024',
            'description' => 'Tirage de Noël',
            'organizer_name' => 'Jean Dupont',
            'organizer_email' => 'jean@example.com',
            'auto_accept_participants' => true,
            'allow_target_messages' => true,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'draw' => ['uuid', 'status'],
                'organizer_link',
                'master_key',
            ]);

        expect(Draw::where('uuid', $response->json('draw.uuid'))->exists())->toBeTrue();
        expect(Draw::where('uuid', $response->json('draw.uuid'))->first()->status)->toBe('draft');
    });

    test('adds participant to draw', function () {
        $createResponse = postJson("{$this->apiPrefix}/draws", [
            'title' => 'Test Draw',
            'organizer_name' => 'Organisateur',
            'organizer_email' => 'org@example.com',
        ]);

        $drawUuid = $createResponse->json('draw.uuid');
        $masterKey = $createResponse->json('master_key');

        $response = postJson(
            "{$this->apiPrefix}/draws/{$drawUuid}/participants",
            [
                'name' => 'Alice',
                'email' => 'alice@example.com',
            ],
            [
                'X-Master-Key' => base64_encode($masterKey),
            ]
        );

        $response->assertStatus(201)
            ->assertJsonStructure([
                'participant' => ['uuid', 'status'],
                'participant_link',
            ]);
    });

    test('requires master key to add participant', function () {
        $draw = Draw::factory()->create();

        $response = postJson(
            "{$this->apiPrefix}/draws/{$draw->uuid}/participants",
            [
                'name' => 'Alice',
                'email' => 'alice@example.com',
            ]
        );

        $response->assertStatus(401)
            ->assertJson(['error' => 'Master key required']);
    });

    test('toggles registration status', function () {
        $draw = Draw::factory()->create(['status' => 'draft']);

        $response = patchJson("{$this->apiPrefix}/draws/{$draw->uuid}/registration", [
            'action' => 'open',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Registrations opened',
                'draw' => ['status' => 'open_registration'],
            ]);

        $response = patchJson("{$this->apiPrefix}/draws/{$draw->uuid}/registration", [
            'action' => 'close',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Registrations closed',
                'draw' => ['status' => 'closed_registration'],
            ]);
    });

    test('launches draw with sufficient participants', function () {
        $draw = Draw::factory()->create(['status' => 'closed_registration']);
        Participant::factory()->count(5)->create([
            'draw_id' => $draw->id,
            'status' => 'accepted',
        ]);

        $response = postJson("{$this->apiPrefix}/draws/{$draw->uuid}/launch");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Draw processing started',
            ]);

        Queue::assertPushed(\App\Jobs\ProcessDrawJob::class);
    });

    test('prevents launch with insufficient participants', function () {
        $draw = Draw::factory()->create(['status' => 'closed_registration']);
        Participant::factory()->count(2)->create([
            'draw_id' => $draw->id,
            'status' => 'accepted',
        ]);

        $response = postJson("{$this->apiPrefix}/draws/{$draw->uuid}/launch");

        $response->assertStatus(422)
            ->assertJsonFragment(['error' => 'At least 3 participants are required (found: 2)']);
    });
});