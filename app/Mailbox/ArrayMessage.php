<?php

namespace App\Mailbox;

use App\Contracts\EmailMessage;
use Illuminate\Mail\SentMessage as BaseSentMessage;

class ArrayMessage extends BaseSentMessage implement EmailMessage
{
    public function getTo() : array
    {
        
    }
}