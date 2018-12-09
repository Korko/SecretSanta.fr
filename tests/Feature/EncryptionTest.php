<?php

namespace Tests\Feature;

use App\Participant;

class EncryptionTest extends RequestCase
{
    // Method used for dear santa
    public function testAsymmetricalEncryption()
    {
        // pubKey is kept in database but privKey is sent to the user
        list('private' => $privKey, 'public' => $pubKey) = AsymmetricalEncrypter::generateKeys();

        $encrypter = new AsymmetricalEncrypter($privKey, $pubKey);

        $challenge = Participant::CHALLENGE;
        $challenge = $encrypter->encrypt($challenge);
    }
}
