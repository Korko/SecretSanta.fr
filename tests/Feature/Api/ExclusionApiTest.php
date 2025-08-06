<?php

use App\Managers\Encryption\SecretSantaEncryptionManager;
use App\Models\Draw\Draw;
use App\Models\Draw\Exclusion;
use App\Models\Draw\Participant;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Exclusion API', function () {
    beforeEach(function () {
        $this->apiPrefix = '/api/v1';
        $this->draw = Draw::factory()->create();
        $this->participants = Participant::factory()->count(5)->create([
            'draw_id' => $this->draw->id,
            'status' => 'accepted',
        ])->toArray();
    });

    test('create exclusion endpoint', function () {
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
    });

    test('create bulk exclusions endpoint', function () {
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
    });

    test('create exclusion group endpoint', function () {
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
    });

    test('validate exclusion constraints endpoint', function () {
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
    });
});
