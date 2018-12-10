<?php

namespace Tests\Feature;

use App\Participant;
use App\Services\AsymmetricalEncrypter;
use App\Services\AsymmetricalPrivateEncrypter;
use App\Services\AsymmetricalPublicEncrypter;

class EncryptionTest extends RequestCase
{
    // Method used for dear santa
    public function testAsymmetricalEncryption()
    {
        // pubKey is kept in database but privKey is sent to the user
        list('private' => $privKey, 'public' => $pubKey) = AsymmetricalEncrypter::generateKeys();

        $challengeRaw = Participant::CHALLENGE;

        $encrypter = new AsymmetricalPublicEncrypter($pubKey);
        $challenge = $encrypter->encrypt($challengeRaw);
        $this->assertNotEquals($challenge, $challengeRaw);

        $decrypter = new AsymmetricalPrivateEncrypter($privKey);
        $challenge = $decrypter->decrypt($challenge);
        $this->assertEquals($challenge, $challengeRaw);
    }
}
