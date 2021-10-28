<?php

namespace App\Mailbox;

use App\Contracts\Mailbox as MailboxContract;
use Illuminate\Mail\Transport\ArrayTransport as BaseArrayTransport;
use Swift_Mime_SimpleMessage;

class ArrayTransport extends BaseArrayTransport implements MailboxContract
{
    /**
     * {@inheritdoc}
     *
     * @return int
     */
    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        $message = new Message($message);

        return parent::send($message, $failedRecipients);
    }

    public function getUnseenMails(): Iterable
    {
        return $this->messages
            ->filter
            ->isUnseen();
    }
}