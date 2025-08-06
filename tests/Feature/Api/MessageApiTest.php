<?php

namespace Tests\Feature\Api;

use App\Managers\Encryption\SecretSantaEncryptionManager;
use App\Models\Draw\Draw;
use App\Models\Draw\Participant;
use App\Models\Message\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessageApiTest extends TestCase
{
    use RefreshDatabase;

    private string $apiPrefix = '/api/v1';
    private Participant $sender;
    private Participant $receiver;
    private string $masterKey;
    private string $individualKey;

    protected function setUp(): void
    {
        parent::setUp();

        $encryptionManager = app(SecretSantaEncryptionManager::class);
        $drawEncryption = $encryptionManager->createDrawEncryption();

        $this->masterKey = $drawEncryption['master_key'];
        $this->individualKey = $drawEncryption['organizer_key'];

        $draw = Draw::factory()->create();

        $this->sender = Participant::factory()->create([
            'draw_id' => $draw->id,
            'status' => 'accepted',
            'individual_key_hash' => $drawEncryption['organizer_key_hash'],
            'master_key_encrypted' => $drawEncryption['master_key_encrypted'],
        ]);

        $participantEncryption = $encryptionManager->addParticipantEncryption($this->masterKey);

        $this->receiver = Participant::factory()->create([
            'draw_id' => $draw->id,
            'status' => 'accepted',
            'assigned_to_participant_id' => $this->sender->id,
        ]);

        $this->sender->update(['assigned_to_participant_id' => $this->receiver->id]);
    }

    public function test_send_message_endpoint()
    {
        $response = $this->postJson(
            "{$this->apiPrefix}/participants/{$this->sender->uuid}/messages",
            [
                'content' => 'Salut ! J\'aimerais un livre !',
                'type' => 'to_secret_santa',
            ],
            [
                'X-Individual-Key' => base64_encode($this->individualKey),
            ]
        );

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'message_data' => ['id', 'type', 'created_at'],
            ]);
    }

    public function test_get_participant_messages_endpoint()
    {
        // Créer quelques messages
        $message = Message::factory()->create([
            'draw_id' => $this->sender->draw_id,
            'from_participant_id' => $this->sender->id,
            'to_participant_id' => $this->receiver->id,
            'type' => 'to_secret_santa',
        ]);

        $response = $this->getJson(
            "{$this->apiPrefix}/participants/{$this->sender->uuid}/messages",
            [
                'X-Individual-Key' => base64_encode($this->individualKey),
            ]
        );

        $response->assertStatus(200)
            ->assertJsonStructure([
                'messages' => [
                    '*' => [
                        'id',
                        'content',
                        'type',
                        'direction',
                        'from',
                        'to',
                        'reactions',
                        'created_at',
                    ],
                ],
            ]);
    }

    public function test_add_reaction_endpoint()
    {
        $message = Message::factory()->create([
            'draw_id' => $this->sender->draw_id,
            'from_participant_id' => $this->sender->id,
            'to_participant_id' => $this->receiver->id,
        ]);

        $response = $this->postJson(
            "{$this->apiPrefix}/messages/{$message->id}/reactions",
            ['reaction' => '👍'],
            ['X-Individual-Key' => base64_encode($this->individualKey)]
        );

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Reaction added successfully',
            ]);

        $this->assertDatabaseHas('message_reactions', [
            'message_id' => $message->id,
            'participant_id' => $this->sender->id,
            'reaction' => '👍',
        ]);
    }

    public function test_report_message_endpoint()
    {
        $message = Message::factory()->create([
            'draw_id' => $this->sender->draw_id,
            'from_participant_id' => $this->receiver->id,
            'to_participant_id' => $this->sender->id,
        ]);

        $response = $this->postJson(
            "{$this->apiPrefix}/messages/{$message->id}/report",
            ['reason' => 'Contenu inapproprié'],
            ['X-Individual-Key' => base64_encode($this->individualKey)]
        );

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Message reported successfully',
            ]);

        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'is_reported' => true,
        ]);
    }
}
