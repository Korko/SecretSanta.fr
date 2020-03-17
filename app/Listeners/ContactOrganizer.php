<?php

namespace App\Listeners;

use App\Exceptions\SolverException;
use App\Events\DrawDone;
use App\Jobs\SendMail;
use App\Mail\OrganizerFinalRecap;
use App\Mail\OrganizerRecap;
use Facades\App\Services\HatSolver as Solver;

class ContactOrganizer
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\DrawDone  $event
     * @return void
     */
    public function handle(DrawDone $event)
    {
        SendMail::dispatch($event->draw->organizer, new OrganizerRecap($event->draw));

        $event->draw->participants->each(function (&$participant) {
            $participant->exclusions[] = $participant->target->id;
        });

        try {
            // Check if there's a solution with the previous exclusions + the actual target
            Solver::one($event->draw->participants->all(), $event->draw->participants->pluck('exclusions', 'id')->all());
        } catch (SolverException $e) {
            // If not, reset all the exclusions
            $event->draw->participants->each(function (&$participant) {
                $participant->exclusions = [];
            });
        }

        SendMail::dispatch($event->draw->organizer, new OrganizerFinalRecap($event->draw))
            ->delay($event->draw->expires_at->addDays(2));
    }
}
