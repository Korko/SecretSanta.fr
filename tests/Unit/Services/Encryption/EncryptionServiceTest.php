<?php

namespace Tests\Unit\Services\Encryption;

use App\Services\Encryption\EncryptionService;
use Tests\TestCase;

class EncryptionServiceTest extends TestCase
{
    private EncryptionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new EncryptionService();
    }

    public function test_generates_valid_key()
    {
        $key = $this->service->generateKey();

        $this->assertIsString($key);
        $this->assertEquals(32, strlen($key)); // 256 bits = 32 bytes
    }

    public function test_encrypts_and_decrypts_data()
    {
        $key = $this->service->generateKey();
        $originalData = 'Secret Santa 2024 - Données confidentielles';

        $encrypted = $this->service->encrypt($originalData, $key);
        $decrypted = $this->service->decrypt($encrypted, $key);

        $this->assertNotEquals($originalData, $encrypted);
        $this->assertEquals($originalData, $decrypted);
    }

    public function test_different_keys_produce_different_ciphertext()
    {
        $key1 = $this->service->generateKey();
        $key2 = $this->service->generateKey();
        $data = 'Test data';

        $encrypted1 = $this->service->encrypt($data, $key1);
        $encrypted2 = $this->service->encrypt($data, $key2);

        $this->assertNotEquals($encrypted1, $encrypted2);
    }

    public function test_cannot_decrypt_with_wrong_key()
    {
        $key1 = $this->service->generateKey();
        $key2 = $this->service->generateKey();
        $data = 'Secret data';

        $encrypted = $this->service->encrypt($data, $key1);

        $this->expectException(\Exception::class);
        $this->service->decrypt($encrypted, $key2);
    }

    public function test_derives_key_from_password()
    {
        $password = 'SecretPassword123!';
        $salt = random_bytes(16);

        $key1 = $this->service->deriveKeyFromPassword($password, $salt);
        $key2 = $this->service->deriveKeyFromPassword($password, $salt);

        // Même mot de passe et même sel = même clé
        $this->assertEquals($key1, $key2);

        // Sel différent = clé différente
        $differentSalt = random_bytes(16);
        $key3 = $this->service->deriveKeyFromPassword($password, $differentSalt);
        $this->assertNotEquals($key1, $key3);
    }
}
