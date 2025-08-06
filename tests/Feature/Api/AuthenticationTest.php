<?php

namespace Tests\Feature\Api;

use App\Managers\Encryption\SecretSantaEncryptionManager;
use App\Models\Draw\Participant;
use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    private string $apiPrefix = '/api/v1';

    public function test_user_registration_endpoint()
    {
        $response = $this->postJson("{$this->apiPrefix}/auth/register", [
            'email' => 'user@example.com',
            'password' => 'SecurePassword123!',
            'password_confirmation' => 'SecurePassword123!',
            'name' => 'John Doe',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'user' => ['id'],
                'token',
            ]);

        $this->assertDatabaseHas('users', [
            'email_hash' => hash('sha256', strtolower('user@example.com')),
        ]);
    }

    public function test_user_login_endpoint()
    {
        // Créer un utilisateur
        $user = User::factory()->create([
            'email_hash' => hash('sha256', 'user@example.com'),
            'password_hash' => bcrypt('password'),
        ]);

        $response = $this->postJson("{$this->apiPrefix}/auth/login", [
            'email' => 'user@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'user' => ['id'],
                'token',
            ]);
    }

    public function test_participant_authentication_endpoint()
    {
        $encryptionManager = app(SecretSantaEncryptionManager::class);
        $participantEncryption = $encryptionManager->createDrawEncryption();

        $participant = Participant::factory()->create([
            'individual_key_hash' => $participantEncryption['organizer_key_hash'],
            'master_key_encrypted' => $participantEncryption['master_key_encrypted'],
        ]);

        $response = $this->postJson("{$this->apiPrefix}/participants/{$participant->uuid}/authenticate", [
            'individual_key' => base64_encode($participantEncryption['organizer_key']),
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'participant' => ['uuid'],
                'access_token',
            ]);
    }
}
