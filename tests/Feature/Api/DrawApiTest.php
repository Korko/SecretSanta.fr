<?php

namespace Tests\Feature\Api;

use App\Models\Draw\Draw;
use App\Models\Draw\Participant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class DrawApiTest extends TestCase
{
    use RefreshDatabase;

    private string $apiPrefix = '/api/v1';

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
    }

    public function test_create_draw_endpoint()
    {
        $response = $this->postJson("{$this->apiPrefix}/draws", [
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

        $this->assertDatabaseHas('draws', [
            'uuid' => $response->json('draw.uuid'),
            'status' => 'draft',
        ]);
    }

    public function test_add_participant_endpoint()
    {
        // Créer un tirage
        $createResponse = $this->postJson("{$this->apiPrefix}/draws", [
            'title' => 'Test Draw',
            'organizer_name' => 'Organisateur',
            'organizer_email' => 'org@example.com',
        ]);

        $drawUuid = $createResponse->json('draw.uuid');
        $masterKey = $createResponse->json('master_key');

        // Ajouter un participant
        $response = $this->postJson(
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
    }

    public function test_add_participant_requires_master_key()
    {
        $draw = Draw::factory()->create();

        $response = $this->postJson(
            "{$this->apiPrefix}/draws/{$draw->uuid}/participants",
            [
                'name' => 'Alice',
                'email' => 'alice@example.com',
            ]
        );

        $response->assertStatus(401)
            ->assertJson(['error' => 'Master key required']);
    }

    public function test_toggle_registration_endpoint()
    {
        $draw = Draw::factory()->create(['status' => 'draft']);

        // Ouvrir les inscriptions
        $response = $this->patchJson("{$this->apiPrefix}/draws/{$draw->uuid}/registration", [
            'action' => 'open',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Registrations opened',
                'draw' => ['status' => 'open_registration'],
            ]);

        // Fermer les inscriptions
        $response = $this->patchJson("{$this->apiPrefix}/draws/{$draw->uuid}/registration", [
            'action' => 'close',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Registrations closed',
                'draw' => ['status' => 'closed_registration'],
            ]);
    }

    public function test_launch_draw_endpoint()
    {
        $draw = Draw::factory()->create(['status' => 'closed_registration']);
        Participant::factory()->count(5)->create([
            'draw_id' => $draw->id,
            'status' => 'accepted',
        ]);

        $response = $this->postJson("{$this->apiPrefix}/draws/{$draw->uuid}/launch");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Draw processing started',
            ]);

        Queue::assertPushed(\App\Jobs\ProcessDraw::class);
    }

    public function test_launch_draw_fails_with_insufficient_participants()
    {
        $draw = Draw::factory()->create(['status' => 'closed_registration']);
        Participant::factory()->count(2)->create([
            'draw_id' => $draw->id,
            'status' => 'accepted',
        ]);

        $response = $this->postJson("{$this->apiPrefix}/draws/{$draw->uuid}/launch");

        $response->assertStatus(422)
            ->assertJsonFragment(['error' => 'At least 3 participants are required (found: 2)']);
    }
}
