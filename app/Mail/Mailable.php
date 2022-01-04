<?php

namespace App\Mail;

use Illuminate\Mail\Mailable as BaseMailable;
use Swift_Signers_DKIMSigner;

class Mailable extends BaseMailable
{
    /**
     * Send the message using the given mailer.
     *
     * @param  \Illuminate\Contracts\Mail\Factory|\Illuminate\Contracts\Mail\Mailer  $mailer
     * @return void
     */
    public function send($mailer)
    {
        // Sign DKIM
        $this->withSwiftMessage(function ($message) {
            if (
                config('mail.dkim_private_key') &&
                file_exists(config('mail.dkim_private_key')) &&
                config('mail.dkim_selector') &&
                config('mail.dkim_domain')
            ) {
                $signer = new Swift_Signers_DKIMSigner(
                    file_get_contents(config('mail.dkim_private_key')),
                    config('mail.dkim_domain'),
                    config('mail.dkim_selector'),
                    config('mail.dkim_passphrase')
                );

                $signer->setHashAlgorithm(config('mail.dkim_algo'));

                if (config('mail.dkim_identity')) {
                    $signer->setSignerIdentity(config('mail.dkim_identity'));
                }

                $message->attachSigner($signer);
            }
        });

        return parent::send($mailer);
    }
}
