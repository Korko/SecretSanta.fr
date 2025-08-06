<?php

namespace Tests\Unit\Actions\Draw;

use App\Actions\Draw\AddParticipantAction;
use App\Actions\Draw\CreateDrawAction;
use App\Managers\Encryption\SecretSantaEncryptionManager;
use App\Models\Draw\Draw;
use App\Models\Draw\Participant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddParticipantActionTest extends TestCase
{
    use RefreshDatabase;

    private AddParticipantAction $action;
    private Draw $draw;
    private string $masterKey;

    protected function setUp(): void
    {
        parent::setUp();

        $encryptionManager = app(SecretSantaEncryptionManager::class);
        $this->action = new AddParticipantAction($encryptionManager);

        // Créer un tirage de test
        $createAction = new CreateDrawAction($encryptionManager);
        $result = $createAction->execute([
            'title' => 'Test Draw',
            'organizer_name' => 'Organisateur',
            'organizer_email' => 'org@example.com',
        ]);

        $this->draw = $result['draw'];
        $this->masterKey = $result['master_key'];
    }

    public function test_adds_participant_successfully()
    {
        $data = [
            'name' => 'Alice',
            'email' => 'alice@example.com',
        ];

        $result = $this->action->execute($this->draw, $data, $this->masterKey);

        $this->assertTrue($result['success']);
        $this->assertInstanceOf(Participant::class, $result['participant']);
        $this->assertNotEmpty($result['participant_link']);

        $this->assertDatabaseHas('participants', [
            'draw_id' => $this->draw->id,
            'uuid' => $result['participant']->uuid,
            'is_organizer' => false,
        ]);
    }

    public function test_auto_accepts_participant_when_enabled()
    {
        $this->draw->update(['auto_accept_participants' => true]);

        $data = [
            'name' => 'Bob',
            'email' => 'bob@example.com',
        ];

        $result = $this->action->execute($this->draw, $data, $this->masterKey);

        $this->assertTrue($result['success']);
        $this->assertEquals('accepted', $result['participant']->status);
        $this->assertNotNull($result['participant']->accepted_at);
    }

    public function test_prevents_duplicate_names()
    {
        // Ajouter un premier participant
        $this->action->execute($this->draw, [
            'name' => 'Charlie',
            'email' => 'charlie1@example.com',
        ], $this->masterKey);

        // Essayer d'ajouter un participant avec le même nom
        $result = $this->action->execute($this->draw, [
            'name' => 'Charlie',
            'email' => 'charlie2@example.com',
        ], $this->masterKey);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('already exists', $result['error']);
    }
}
