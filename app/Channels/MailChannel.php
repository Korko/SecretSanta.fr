<?php

namespace App\Channels;

use Closure;
use Illuminate\Notifications\Channels\MailChannel as BaseMailChannel;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Swift_Signers_DKIMSigner;

class MailChannel extends BaseMailChannel
{
    /**
     * Get the mailer Closure for the message.
     *
     * @param  mixed  $notifiable
     * @param  Notification  $notification
     * @param  MailMessage  $message
     * @return Closure
     */
    protected function messageBuilder($notifiable, $notification, $message)
    {
        // Sign DKIM
        $message->withSymfonyMessage(function ($message) {
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

        return parent::messageBuilder($notifiable, $notification, $message);
    }
}
