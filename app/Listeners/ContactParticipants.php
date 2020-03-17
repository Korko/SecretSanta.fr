<?php

namespace App\Listeners;

use App\Events\DrawDone;
use App\Jobs\SendMail;
use App\Mail\TargetDrawn;
use Metrics;

class ContactParticipants
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(DrawDone $event)
    {
        foreach ($event->draw->participants as $participant) {
            Metrics::increment('email');

            SendMail::dispatch($participant, new TargetDrawn($participant));
        }
    }
}
