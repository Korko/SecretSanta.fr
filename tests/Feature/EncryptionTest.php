<?php

namespace Tests\Feature;

use App\Services\SymmetricalEncrypter;
use App\Services\AsymmetricalEncrypter;
use App\Services\AsymmetricalPublicEncrypter;
use App\Services\AsymmetricalPrivateEncrypter;

class EncryptionTest extends RequestCase
{
    public function testAsymmetricalEncryption(): void
    {
        list('private' => $privKey, 'public' => $pubKey) = AsymmetricalEncrypter::generateKeys();

        $challengeRaw = 'challenge';

        // Encrypted with public
        $encrypter = new AsymmetricalPublicEncrypter($pubKey);
        $challenge = $encrypter->encrypt($challengeRaw);
        $this->assertNotEquals($challenge, $challengeRaw);

        $decrypter = new AsymmetricalPrivateEncrypter($privKey);
        $challenge = $decrypter->decrypt($challenge);
        $this->assertEquals($challenge, $challengeRaw);

        // Encrypter with private
        $encrypter = new AsymmetricalPrivateEncrypter($privKey);
        $challenge = $encrypter->encrypt($challengeRaw);
        $this->assertNotEquals($challenge, $challengeRaw);

        $decrypter = new AsymmetricalPublicEncrypter($pubKey);
        $challenge = $decrypter->decrypt($challenge);
        $this->assertEquals($challenge, $challengeRaw);
    }

    public function testSymmetricalEncryption(): void
    {
        $key = SymmetricalEncrypter::generateKey(config('app.cipher'));

        $challengeRaw = 'challenge';

        $encrypter = new SymmetricalEncrypter($key);

        $challenge = $encrypter->encrypt($challengeRaw);
        $this->assertNotEquals($challenge, $challengeRaw);

        $challenge = $encrypter->decrypt($challenge);
        $this->assertEquals($challenge, $challengeRaw);
    }
}