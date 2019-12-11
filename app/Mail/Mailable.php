<?php

namespace App\Mail;

use Illuminate\Mail\Mailable as BaseMailable;
use Illuminate\Contracts\Mail\Mailer as MailerContract;

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
