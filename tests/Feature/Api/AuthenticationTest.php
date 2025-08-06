<?php

use App\Managers\Encryption\SecretSantaEncryptionManager;
use App\Models\Draw\Participant;
use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Authentication API', function () {
    beforeEach(function () {
        $this->apiPrefix = '/api/v1';
    });

    test('user registration endpoint', function () {
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
    });

    test('user login endpoint', function () {
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
    });

    test('participant authentication endpoint', function () {
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
    });
});
