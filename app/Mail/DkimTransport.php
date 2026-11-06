<?php

declare(strict_types=1);

namespace App\Mail;

use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Crypto\DkimSigner;
use Symfony\Component\Mime\Message;
use Symfony\Component\Mime\RawMessage;

/**
 * Signs every outgoing message with DKIM, then hands it to the real transport.
 */
final readonly class DkimTransport implements TransportInterface
{
    public function __construct(
        private TransportInterface $transport,
        private DkimSigner $signer,
    ) {}

    public function __toString(): string
    {
        return 'dkim+'.$this->transport;
    }

    public function send(RawMessage $message, ?Envelope $envelope = null): ?SentMessage
    {
        if ($message instanceof Message) {
            // The envelope must be computed from the unsigned message (Return-Path drives bounces)
            $envelope ??= Envelope::create($message);
            $message = $this->signer->sign($message);
        }

        return $this->transport->send($message, $envelope);
    }
}
