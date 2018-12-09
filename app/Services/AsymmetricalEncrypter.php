<?php

namespace App\Services;

use Illuminate\Contracts\Encryption\Encrypter;

abstract class AsymmetricalEncrypter implements Encrypter
{
    public static function generateKeys()
    {
        // Create the private and public key
        $res = openssl_pkey_new([
            'digest_alg'       => 'sha512',
            'private_key_bits' => 4096,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);

        // Extract the private key from $res to $privKey
        openssl_pkey_export($res, $privKey);

        // Extract the public key from $res to $pubKey
        $pubKey = openssl_pkey_get_details($res);
        $pubKey = $pubKey['key'];

        return [
            'private' => $privKey,
            'public'  => $pubKey,
        ];
    }
}
