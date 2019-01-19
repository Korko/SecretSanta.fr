<?php

namespace Tests\Feature;

use App\DearSanta;
use App\Services\AsymmetricalEncrypter;
use App\Services\AsymmetricalPrivateEncrypter;
use App\Services\AsymmetricalPublicEncrypter;
use App\Services\SymmetricalEncrypter;

class EncryptionTest extends RequestCase
{
    public function testAsymmetricalEncryption()
    {
        list('private' => $privKey, 'public' => $pubKey) = AsymmetricalEncrypter::generateKeys();

        $challengeRaw = config('app.challenge');

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

    public function testSymmetricalEncryption()
    {
        $key = SymmetricalEncrypter::generateKey(config('app.cipher'));

        $challengeRaw = config('app.challenge');

        $encrypter = new SymmetricalEncrypter($key);

        $challenge = $encrypter->encrypt($challengeRaw);
        $this->assertNotEquals($challenge, $challengeRaw);

        $challenge = $encrypter->decrypt($challenge);
        $this->assertEquals($challenge, $challengeRaw);
    }
}
