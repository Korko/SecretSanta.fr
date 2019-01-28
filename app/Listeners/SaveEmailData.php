<?php

namespace App\Listeners;

use Illuminate\Mail\Events\MessageSent;

class SaveEmailData
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        if (isset($event->data->participant)) {
            $participant = $event->data->participant;
dump($event->data);
            $participant->email_id = $event->message->getHeaders()->get('X-Message-Id')->getValue();
            $participant->save();
dump($participant);
        }
    }
}
