<?php

namespace App\Mail;

use Illuminate\Contracts\Mail\Mailer as MailerContract;
use Illuminate\Mail\Mailable as BaseMailable;

abstract class Mailable extends BaseMailable
{
    public function send(MailerContract $mailer)
    {
        //Initializes properties on the Swift Message object
        $this->withSwiftMessage(function ($message) {
            $message->mailable = get_class($this);
        });

        parent::send($mailer);
    }
}
