<?php

namespace Tests\Unit\Services\Encryption;

use App\Managers\Encryption\SecretSantaEncryptionManager;
use Tests\TestCase;

class SecretSantaEncryptionManagerTest extends TestCase
{
    private SecretSantaEncryptionManager $manager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = new SecretSantaEncryptionManager();
    }

    public function test_creates_draw_encryption_system()
    {
        $result = $this->manager->createDrawEncryption();

        $this->assertArrayHasKey('master_key', $result);
        $this->assertArrayHasKey('organizer_key', $result);
        $this->assertArrayHasKey('organizer_key_hash', $result);
        $this->assertArrayHasKey('master_key_encrypted', $result);

        // Vérifier que les clés sont différentes
        $this->assertNotEquals($result['master_key'], $result['organizer_key']);
    }

    public function test_adds_participant_with_unique_key()
    {
        $drawEncryption = $this->manager->createDrawEncryption();
        $masterKey = $drawEncryption['master_key'];

        $participant1 = $this->manager->addParticipantEncryption($masterKey);
        $participant2 = $this->manager->addParticipantEncryption($masterKey);

        // Chaque participant a sa propre clé
        $this->assertNotEquals($participant1['participant_key'], $participant2['participant_key']);
        $this->assertNotEquals($participant1['participant_key_hash'], $participant2['participant_key_hash']);

        // Mais ils peuvent tous déchiffrer la même clé master
        $this->assertNotEmpty($participant1['master_key_encrypted']);
        $this->assertNotEmpty($participant2['master_key_encrypted']);
    }

    public function test_validates_and_retrieves_master_key()
    {
        $drawEncryption = $this->manager->createDrawEncryption();
        $masterKey = $drawEncryption['master_key'];
        $organizerKey = $drawEncryption['organizer_key'];

        $retrievedMasterKey = $this->manager->validateAndGetMasterKey(
            $drawEncryption['master_key_encrypted'],
            $organizerKey,
            $drawEncryption['organizer_key_hash']
        );

        $this->assertEquals($masterKey, $retrievedMasterKey);
    }

    public function test_fails_with_invalid_individual_key()
    {
        $drawEncryption = $this->manager->createDrawEncryption();
        $wrongKey = $this->manager->getIndividualKeyManager()->generateIndividualKey();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid individual key');

        $this->manager->validateAndGetMasterKey(
            $drawEncryption['master_key_encrypted'],
            $wrongKey,
            $drawEncryption['organizer_key_hash']
        );
    }

    public function test_generates_participant_link()
    {
        $baseUrl = 'https://secretsanta.fr';
        $drawUuid = 'draw-123';
        $participantUuid = 'participant-456';
        $individualKey = $this->manager->getIndividualKeyManager()->generateIndividualKey();

        $link = $this->manager->getIndividualKeyManager()->generateParticipantLink(
            $baseUrl,
            $drawUuid,
            $participantUuid,
            $individualKey
        );

        $this->assertStringStartsWith($baseUrl, $link);
        $this->assertStringContainsString($drawUuid, $link);
        $this->assertStringContainsString($participantUuid, $link);
        $this->assertStringContainsString('#', $link);

        // Vérifier qu'on peut extraire la clé du lien
        $parts = explode('#', $link);
        $extractedKey = $this->manager->getIndividualKeyManager()->extractKeyFromLink($parts[1]);
        $this->assertEquals($individualKey, $extractedKey);
    }
}
