<?php

namespace App\Listeners;

use Log;
use App\Participant;
use Illuminate\Mail\Events\MessageSent;

class EmailSent
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
     * @param  \Illuminate\Mail\Events\MessageSent  $event
     * @return void
     */
    public function handle(MessageSent $event)
    {
        if (isset($event->data->participantId)) {
            $participant = Participant::find($event->data->participantId);
            $participant->delivery_status = Participant::SENT;
            $participant->save();
        }
    }

    /**
     * Handle a job failure.
     *
     * @param  \Illuminate\Mail\Events\MessageSent  $event
     * @param  \Exception  $exception
     * @return void
     */
    public function failed(MessageSent $event, Exception $exception)
    {
        if (isset($event->data->participantId)) {
            $participant = Participant::find($event->data->participantId);
            $participant->delivery_status = Participant::ERROR;
            $participant->save();

            Log::debug(var_export($event->message->getHeaders(), true));
        }
    }
}
