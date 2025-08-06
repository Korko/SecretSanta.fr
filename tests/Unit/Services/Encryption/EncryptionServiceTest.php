<?php

use App\Services\Encryption\EncryptionService;

describe('EncryptionService', function () {
    beforeEach(function () {
        $this->service = new EncryptionService();
    });

    test('generates valid key', function () {
        $key = $this->service->generateKey();

        expect($key)->toBeString();
        expect(strlen($key))->toBe(32);
    });

    test('encrypts and decrypts data', function () {
        $key = $this->service->generateKey();
        $originalData = 'Secret Santa 2024 - Données confidentielles';

        $encrypted = $this->service->encrypt($originalData, $key);
        $decrypted = $this->service->decrypt($encrypted, $key);

        expect($encrypted)->not->toEqual($originalData);
        expect($decrypted)->toEqual($originalData);
    });

    test('different keys produce different ciphertext', function () {
        $key1 = $this->service->generateKey();
        $key2 = $this->service->generateKey();
        $data = 'Test data';

        $encrypted1 = $this->service->encrypt($data, $key1);
        $encrypted2 = $this->service->encrypt($data, $key2);

        expect($encrypted1)->not->toEqual($encrypted2);
    });

    test('cannot decrypt with wrong key', function () {
        $key1 = $this->service->generateKey();
        $key2 = $this->service->generateKey();
        $data = 'Secret data';

        $encrypted = $this->service->encrypt($data, $key1);

        expect(fn () => $this->service->decrypt($encrypted, $key2))
            ->toThrow(Exception::class);
    });

    test('derives key from password', function () {
        $password = 'SecretPassword123!';
        $salt = random_bytes(16);

        $key1 = $this->service->deriveKeyFromPassword($password, $salt);
        $key2 = $this->service->deriveKeyFromPassword($password, $salt);

        expect($key1)->toEqual($key2);

        $differentSalt = random_bytes(16);
        $key3 = $this->service->deriveKeyFromPassword($password, $differentSalt);
        expect($key1)->not->toEqual($key3);
    });
});
