<?php

use App\Managers\Encryption\SecretSantaEncryptionManager;

describe('SecretSantaEncryptionManager', function () {
    beforeEach(function () {
        $this->manager = new SecretSantaEncryptionManager();
    });

    test('creates draw encryption system', function () {
        $result = $this->manager->createDrawEncryption();

        expect($result)->toHaveKey('master_key');
        expect($result)->toHaveKey('organizer_key');
        expect($result)->toHaveKey('organizer_key_hash');
        expect($result)->toHaveKey('master_key_encrypted');

        expect($result['master_key'])->not->toEqual($result['organizer_key']);
    });

    test('adds participant with unique key', function () {
        $drawEncryption = $this->manager->createDrawEncryption();
        $masterKey = $drawEncryption['master_key'];

        $participant1 = $this->manager->addParticipantEncryption($masterKey);
        $participant2 = $this->manager->addParticipantEncryption($masterKey);

        expect($participant1['participant_key'])->not->toEqual($participant2['participant_key']);
        expect($participant1['participant_key_hash'])->not->toEqual($participant2['participant_key_hash']);

        expect($participant1['master_key_encrypted'])->not->toBeEmpty();
        expect($participant2['master_key_encrypted'])->not->toBeEmpty();
    });

    test('validates and retrieves master key', function () {
        $drawEncryption = $this->manager->createDrawEncryption();
        $masterKey = $drawEncryption['master_key'];
        $organizerKey = $drawEncryption['organizer_key'];

        $retrievedMasterKey = $this->manager->validateAndGetMasterKey(
            $drawEncryption['master_key_encrypted'],
            $organizerKey,
            $drawEncryption['organizer_key_hash']
        );

        expect($retrievedMasterKey)->toEqual($masterKey);
    });

    test('fails with invalid individual key', function () {
        $drawEncryption = $this->manager->createDrawEncryption();
        $wrongKey = $this->manager->getIndividualKeyManager()->generateIndividualKey();

        expect(fn () => $this->manager->validateAndGetMasterKey(
            $drawEncryption['master_key_encrypted'],
            $wrongKey,
            $drawEncryption['organizer_key_hash']
        ))->toThrow(Exception::class, 'Invalid individual key');
    });

    test('generates participant link', function () {
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

        expect($link)->toStartWith($baseUrl);
        expect($link)->toContain($drawUuid);
        expect($link)->toContain($participantUuid);
        expect($link)->toContain('#');

        $parts = explode('#', $link);
        $extractedKey = $this->manager->getIndividualKeyManager()->extractKeyFromLink($parts[1]);
        expect($extractedKey)->toEqual($individualKey);
    });
});
