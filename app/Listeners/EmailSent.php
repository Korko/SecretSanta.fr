<?php

namespace App\Listeners;

use Log;
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
        Participant::find($event->data->participantId)
            ->fill(['delivery_status' => Participant::SENT,
        ])->save();
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
        Participant::find($event->data->participantId)
            ->fill(['delivery_status' => Participant::ERROR,
        ])->save();
        Log::debug(var_export($event->message->getHeaders(), true));
    }
}
