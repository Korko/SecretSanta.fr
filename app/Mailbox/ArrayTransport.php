<?php

namespace App\Mailbox;

use App\Contracts\Mailbox as MailboxContract;
use Illuminate\Mail\Transport\ArrayTransport as BaseArrayTransport;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mime\Address;

class ArrayTransport extends BaseArrayTransport implements MailboxContract
{
    /**
     * {@inheritdoc}
     */
    public function getUnseenMails(): iterable
    {
        return $this->messages()
            ->map(function (SentMessage $message) {
                return array_map(fn (Address $recipient) => $recipient->getAddress(), $message->getEnvelope()->getRecipients());
            });
    }
}
