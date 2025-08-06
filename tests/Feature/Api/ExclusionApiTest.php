<?php

namespace Tests\Feature\Api;

use App\Managers\Encryption\SecretSantaEncryptionManager;
use App\Models\Draw\Draw;
use App\Models\Draw\Exclusion;
use App\Models\Draw\Participant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExclusionApiTest extends TestCase
{
    use RefreshDatabase;

    private string $apiPrefix = '/api/v1';
    private Draw $draw;
    private array $participants;

    protected function setUp(): void
    {
        parent::setUp();

        $this->draw = Draw::factory()->create();
        $this->participants = Participant::factory()->count(5)->create([
            'draw_id' => $this->draw->id,
            'status' => 'accepted',
        ])->toArray();
    }

    public function test_create_exclusion_endpoint()
    {
        $response = $this->postJson("{$this->apiPrefix}/draws/{$this->draw->uuid}/exclusions", [
            'participant_id' => $this->participants[0]['id'],
            'excluded_participant_id' => $this->participants[1]['id'],
            'type' => 'strong',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'exclusion' => ['id', 'type', 'source'],
            ]);

        $this->assertDatabaseHas('exclusions', [
            'participant_id' => $this->participants[0]['id'],
            'excluded_participant_id' => $this->participants[1]['id'],
            'type' => 'strong',
            'source' => 'manual',
        ]);
    }

    public function test_create_bulk_exclusions_endpoint()
    {
        $exclusions = [
            [
                'participant_id' => $this->participants[0]['id'],
                'excluded_participant_id' => $this->participants[1]['id'],
                'type' => 'strong',
            ],
            [
                'participant_id' => $this->participants[0]['id'],
                'excluded_participant_id' => $this->participants[2]['id'],
                'type' => 'weak',
            ],
        ];

        $response = $this->postJson("{$this->apiPrefix}/draws/{$this->draw->uuid}/exclusions/bulk", [
            'exclusions' => $exclusions,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'created' => 2,
                'errors' => [],
            ]);
    }

    public function test_create_exclusion_group_endpoint()
    {
        $encryptionManager = app(SecretSantaEncryptionManager::class);
        $masterKey = $encryptionManager->generateMasterKey();

        $response = $this->postJson(
            "{$this->apiPrefix}/draws/{$this->draw->uuid}/exclusion-groups",
            ['name' => 'Famille Dupont'],
            ['X-Master-Key' => base64_encode($masterKey)]
        );

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'group' => ['id'],
            ]);
    }

    public function test_validate_exclusion_constraints_endpoint()
    {
        // Créer des exclusions qui rendent le tirage impossible
        foreach ($this->participants as $index => $participant) {
            foreach ($this->participants as $otherIndex => $otherParticipant) {
                if ($index !== $otherIndex) {
                    Exclusion::create([
                        'draw_id' => $this->draw->id,
                        'participant_id' => $participant['id'],
                        'excluded_participant_id' => $otherParticipant['id'],
                        'type' => 'strong',
                        'source' => 'manual',
                    ]);
                }
            }
        }

        $response = $this->getJson("{$this->apiPrefix}/draws/{$this->draw->uuid}/exclusions/validate");

        $response->assertStatus(200)
            ->assertJson([
                'valid' => false,
            ])
            ->assertJsonStructure([
                'valid',
                'errors',
                'warnings',
            ]);
    }
}
