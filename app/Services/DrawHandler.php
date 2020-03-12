<?php

namespace App\Services;

use App\Draw;
use App\Exceptions\SolverException;
use App\Jobs\SendMail;
use App\Mail as MailModel;
use App\Mail\OrganizerFinalRecap;
use App\Mail\OrganizerRecap;
use App\Mail\TargetDrawn;
use App\Participant;
use Arr;
use Facades\App\Services\HatSolver as Solver;
use Metrics;

class DrawHandler
{
    public function contactParticipants(array $participants, array $mailContent, $dataExpiration): void
    {
        $hat = Solver::one($participants, array_column($participants, 'exclusions'));

        Metrics::increment('draws');
        Metrics::increment('participants', count($participants));

        $draw = new Draw();
        $draw->expires_at = $dataExpiration;
        $draw->mail_title = $mailContent['title'];
        $draw->mail_body = $mailContent['body'];
        $draw->save();

        $draw->participants = collect();
        foreach ($participants as $idx => $santa) {
            $participant = new Participant();
            $participant->draw()->associate($draw);
            $participant->name = $santa['name'];
            $participant->address = Arr::get($santa, 'email');
            $participant->exclusions = $santa['exclusions'];
            $participant->mail()->associate(MailModel::create());
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
        SendMail::dispatch($draw->organizer, new OrganizerRecap($draw));

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

        SendMail::dispatch($draw->organizer, new OrganizerFinalRecap($draw))
            ->delay($draw->expires_at->addDays(2));
    }

    public function informParticipants(Draw $draw): void
    {
        foreach ($draw->participants as $participant) {
            Metrics::increment('email');

            SendMail::dispatch($participant, new TargetDrawn($participant));
        }
    }
}
