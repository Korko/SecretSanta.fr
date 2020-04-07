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
    protected $participants;
    protected $hat;
    protected $expirationDate;

    public function toParticipants(array $participants): self
    {
        $this->participants = $participants;
        $this->hat = Solver::one($participants, array_column($participants, 'exclusions'));

        return $this;
    }

    public function expiresAt($expirationDate): self
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    public function sendMail($title, $body): void
    {
        $draw = $this->createDraw($title, $body, $this->expirationDate ?: date('Y-m-d', strtotime('+2 days')));
        Metrics::increment('draws');

        $this->createParticipants($draw, $this->participants, $this->hat);
        Metrics::increment('participants', count($this->participants));

        $this->contactOrganizer($draw);
        foreach ($draw->participants as $participant) {
            $this->contactParticipant($participant);
        }
    }

    public function createDraw($title, $body, $dataExpiration): Draw
    {
        $draw = new Draw();
        $draw->expires_at = $dataExpiration;
        $draw->mail_title = $title;
        $draw->mail_body = $body;
        $draw->save();

        return $draw;
    }

    public function createParticipants(Draw $draw, array $participants, array $hat): void
    {
        $draw->participants = collect();
        foreach ($participants as $idx => $santa) {
            $participant = new Participant();
            $participant->draw()->associate($draw);
            $participant->name = $santa['name'];
            $participant->email = Arr::get($santa, 'email');
            $participant->exclusions = $santa['exclusions'];
            $participant->mail()->associate(MailModel::create());
            $participant->save();

            $participants[$idx] = $participant;
            $draw->participants->add($participant);
        }

        foreach ($hat as $santaIdx => $targetIdx) {
            $draw->participants[$santaIdx]->exclusions = array_map(function ($participantId) use ($participants) {
                return $participants[$participantId]->id;
            }, $participants[$santaIdx]->exclusions);
            $draw->participants[$santaIdx]->target()->save($participants[$targetIdx]);
            $draw->participants[$santaIdx]->save();
        }
    }

    public function contactOrganizer(Draw $draw, bool $withDelayedEmail = true)
    {
        $this->sendOrganizerEmail($draw);

        if ($withDelayedEmail) {
            $this->sendDelayedOrganizerEmail($draw);
        }
    }

    protected function sendOrganizerEmail(Draw $draw)
    {
        SendMail::dispatch($draw->organizer, new OrganizerRecap($draw));
    }

    protected function sendDelayedOrganizerEmail(Draw $draw)
    {
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

    public function contactParticipant(Participant $participant)
    {
        Metrics::increment('email');

        SendMail::dispatch($participant, new TargetDrawn($participant));
    }
}
