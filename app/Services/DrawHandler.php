<?php

namespace App\Services;

use App\Models\Draw;
use App\Exceptions\SolverException;
use App\Jobs\SendMail;
use App\Models\Mail as MailModel;
use App\Mail\OrganizerRecap;
use App\Mail\TargetDrawn;
use App\Models\Participant;
use Arr;
use Metrics;
use Solver;

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

        if (! $this->isNextDrawSolvable($draw)) {
            $draw->next_solvable = false;
            $draw->save();
        }

        $this->sendOrganizerEmail($draw);
        foreach ($draw->participants as $participant) {
            $this->sendParticipantEmail($participant);
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
        foreach ($participants as $idx => $santa) {
            $mail = (new MailModel())->draw()->associate($draw);
            $mail->save();

            $participant = new Participant();
            $participant->draw()->associate($draw);
            $participant->name = $santa['name'];
            $participant->email = Arr::get($santa, 'email');
            $participant->mail()->associate($mail);
            $participant->save();

            $participants[$idx]['model'] = $participant;
            $draw->participants()->save($participant);
        }

        foreach ($hat as $santaIdx => $targetIdx) {
            foreach ($participants[$santaIdx]['exclusions'] as $participantId) {
                $draw->participants[$santaIdx]->exclusions()->attach($participants[$participantId]['model']->id);
            }

            $draw->participants[$santaIdx]->target()->save($participants[$targetIdx]['model']);
        }
    }

    public function isNextDrawSolvable(Draw $draw): bool
    {
        try {
            $exclusions = [];

            $draw->participants->each(function (Participant $participant) use (&$exclusions) {
                $exclusions[$participant->id] = array_merge(
                    $participant->exclusions->pluck('id')->all(),
                    [$participant->target->id]
                );
            });

            Solver::one($draw->participants->pluck(null, 'id')->all(), $exclusions);

            return true;
        } catch (SolverException $exception) {
            return false;
        }
    }

    public function sendOrganizerEmail(Draw $draw)
    {
        SendMail::dispatch($draw->organizer, new OrganizerRecap($draw));
    }

    public function sendParticipantEmail(Participant $participant)
    {
        Metrics::increment('email');

        SendMail::dispatch($participant, new TargetDrawn($participant));
    }
}
