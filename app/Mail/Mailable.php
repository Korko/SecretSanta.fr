<?php

namespace App\Mail;

use Illuminate\Mail\Mailable as BaseMailable;
use Symfony\Component\Mime\Crypto\DkimSigner;
use Symfony\Component\Mime\Email;

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
        $this->withSymfonyMessage(function (Email $message) {
            if (
                config('mail.dkim_private_key') &&
                file_exists(config('mail.dkim_private_key')) &&
                config('mail.dkim_selector') &&
                config('mail.dkim_domain')
            ) {
                $signer = new DkimSigner(
                    file_get_contents(config('mail.dkim_private_key')),
                    config('mail.dkim_domain'),
                    config('mail.dkim_selector'),
                    config('mail.dkim_passphrase')
                );

                $signer->sign($message);
            }
        });

        return parent::send($mailer);
    }
}
