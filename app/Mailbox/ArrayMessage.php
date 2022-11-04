<?php

namespace App\Mailbox;

use App\Contracts\EmailMessage;
use Illuminate\Mail\SentMessage as BaseSentMessage;

class ArrayMessage extends BaseSentMessage implements EmailMessage
{
    public function getTo(): array
    {
    }
}
