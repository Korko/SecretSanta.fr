<?php

namespace App\Listeners;

use Illuminate\Mail\Events\MessageSent;

class SaveEmailData
{
    protected $crypter;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param MessageSent $event
     *
     * @return void
     */
    public function handle(MessageSent $event)
    {
        // Update Participant to add message_id
//        $mailBody->mail_id = $event->message->getHeaders()->get('X-Message-Id')->getValue(); // TODO, get sendgrid id, needed in case of bounce event
    }
}
