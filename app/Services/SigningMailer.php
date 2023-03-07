<?php

namespace App\Services;

use Symfony\Component\Mailer\SentMessage;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Traits\ForwardsCalls;
use Symfony\Component\Mime\Email;

class SigningMailer
{
    use ForwardsCalls;

    protected $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Send a Symfony Email instance.
     *
     * @param  \Symfony\Component\Mime\Email  $message
     * @return \Symfony\Component\Mailer\SentMessage|null
     */
    protected function sendSymfonyMessage(Email $message): ?SentMessage
    {
        $this->sign($message);

        return $this->forwardCallTo($this->mailer, 'sendSymfonyMessage', [$message]);
    }

    /**
     * Sign a Symfony Email instance with DKIM.
     *
     * @param  \Symfony\Component\Mime\Email  $message
     * @return void
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
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        return $this->forwardCallTo($this->mailer, $method, $parameters);
    }
}
