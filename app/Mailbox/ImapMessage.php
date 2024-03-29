<?php

namespace App\Mailbox;

use App\Contracts\EmailMessage;
use Webklex\PHPIMAP\Exceptions\MessageHeaderFetchingException;
use Webklex\PHPIMAP\Message;

class ImapMessage implements EmailMessage
{
    public function __construct(
        protected readonly Message $message
    ) {
    }

    public function getTo(): array
    {
        return array_filter(
            array_map(function ($message) {
                return (string) $message[0]->mailbox ?? '';
            }, (array) $this->message->getTo())
        );
    }

    public function __destruct()
    {
        try {
            $this->message->move(config('imap.folders.trash'));
        } catch (MessageHeaderFetchingException $e) {
            // Ignore that error
        }
    }
}
