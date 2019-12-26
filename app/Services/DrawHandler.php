<?php

namespace App\Services;

use Arr;
use Mail;
use Metrics;
use App\Draw;
use App\Participant;
use App\Mail\TargetDrawn;
use App\Mail\OrganizerRecap;
use App\Mail\OrganizerFinalRecap;
use App\Exceptions\SolverException;
use Facades\App\Services\HatSolver as Solver;

class DrawHandler
{
    public function contactParticipants(array $participants, array $mailContent, $dataExpiration): void
    {
        $hat = Solver::one($participants, array_column($participants, 'exclusions'));

        Metrics::increment('draws');
        Metrics::increment('participants', count($participants));

        $draw = new Draw();
        $draw->expires_at = $dataExpiration;
        $draw->email_title = $mailContent['title'];
        $draw->email_body = $mailContent['body'];
        $draw->save();

        $draw->participants = collect();
        foreach ($participants as $idx => $santa) {
            $participant = new Participant();
            $participant->draw()->associate($draw);
            $participant->name = $santa['name'];
            $participant->email_address = Arr::get($santa, 'email');
            $participant->exclusions = $santa['exclusions'];
            $participant->save();

            $participants[$idx] = $participant;
            $draw->participants->add($participant);
        }

        foreach ($hat as $santaIdx => $targetIdx) {
            $participants[$santaIdx]->exclusions = array_map(function ($participantId) use ($participants) {
                return $participants[$participantId]->id;
            }, $participants[$santaIdx]->exclusions);
            $participants[$santaIdx]->target()->save($participants[$targetIdx]);
            $participants[$santaIdx]->save();
        }

        $this->informOrganizer($draw);
        $this->informParticipants($draw);
    }

    public function informOrganizer(Draw $draw): void
    {
        Mail::to([['email' => $draw->organizer->email_address, 'name' => $draw->organizer->name]])
            ->queue(new OrganizerRecap($draw));

        $draw->participants->each(function (&$participant) {
            $participant->exclusions[] = $participant->target->id;
        });

        try {
            // Check if there's a solution with the previous exclusions + the actual target
            Solver::one($draw->participants->all(), $draw->participants->pluck('exclusions', 'id')->all());
        } catch (SolverException $e) {
            // If not, reset all the exclusions
            $draw->participants->each(function (&$participant) {
                $participant->exclusions = [];
            });
        }

        $when = $draw->expires_at->addDays(2);
        Mail::to([['email' => $draw->organizer->email_address, 'name' => $draw->organizer->name]])
            ->later($when, new OrganizerFinalRecap($draw));
    }

    public function informParticipants(Draw $draw): void
    {
        foreach ($draw->participants as $participant) {
            Metrics::increment('email');

            Mail::to([['email' => $participant->email_address, 'name' => $participant->name]])
                ->queue(new TargetDrawn($participant));
        }
    }
}
