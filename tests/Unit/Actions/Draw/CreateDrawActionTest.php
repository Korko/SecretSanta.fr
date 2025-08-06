<?php

namespace Tests\Unit\Actions\Draw;

use App\Actions\Draw\CreateDrawAction;
use App\Managers\Encryption\SecretSantaEncryptionManager;
use App\Models\Draw\Draw;
use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateDrawActionTest extends TestCase
{
    use RefreshDatabase;

    private CreateDrawAction $action;
    private SecretSantaEncryptionManager $encryptionManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->encryptionManager = app(SecretSantaEncryptionManager::class);
        $this->action = new CreateDrawAction($this->encryptionManager);
    }

    public function test_creates_draw_successfully()
    {
        $data = [
            'title' => 'Secret Santa 2024',
            'description' => 'Tirage pour Noël',
            'organizer_name' => 'Jean Dupont',
            'organizer_email' => 'jean@example.com',
            'auto_accept_participants' => true,
            'allow_target_messages' => true,
        ];

        $result = $this->action->execute($data);

        $this->assertTrue($result['success']);
        $this->assertInstanceOf(Draw::class, $result['draw']);
        $this->assertNotEmpty($result['organizer_link']);
        $this->assertNotEmpty($result['master_key']);

        // Vérifier en base de données
        $this->assertDatabaseHas('draws', [
            'uuid' => $result['draw']->uuid,
            'status' => 'draft'
        ]);

        // Vérifier qu'un participant organisateur a été créé
        $this->assertDatabaseHas('participants', [
            'draw_id' => $result['draw']->id,
            'is_organizer' => true,
            'status' => 'accepted'
        ]);
    }

    public function test_creates_draw_with_user()
    {
        $user = User::factory()->create();

        $data = [
            'title' => 'Secret Santa 2024',
            'organizer_name' => 'Jean Dupont',
            'organizer_email' => 'jean@example.com',
        ];

        $result = $this->action->execute($data, $user);

        $this->assertTrue($result['success']);
        $this->assertEquals($user->id, $result['draw']->user_id);
    }

    public function test_handles_creation_failure()
    {
        $data = [
            // Données incomplètes
            'title' => '',
        ];

        $result = $this->action->execute($data);

        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('error', $result);
    }
}
