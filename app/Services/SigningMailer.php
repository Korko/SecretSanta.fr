<?php

namespace App\Services;

use Illuminate\Mail\Mailer;
use Illuminate\Support\Traits\ForwardsCalls;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mime\Email;

class SigningMailer
{
    use ForwardsCalls;

    public function __construct(
        protected readonly Mailer $mailer
    ) {
    }

    /**
     * Send a Symfony Email instance.
     */
    protected function sendSymfonyMessage(Email $message): ?SentMessage
    {
        $this->sign($message);

        return $this->forwardCallTo($this->mailer, 'sendSymfonyMessage', [$message]);
    }

    /**
     * Sign a Symfony Email instance with DKIM.
     */
    protected function sign(Email $message): void
    {
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
                [],
                config('mail.dkim_passphrase')
            );

            $signer->sign($message);
        }
    }

    /**
     * Handle dynamic method calls into the original mailer.
     *
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        return $this->forwardCallTo($this->mailer, $method, $parameters);
    }
}
