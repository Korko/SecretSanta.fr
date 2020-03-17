<?php

namespace App\Services;

use App\Draw;
use App\Events\DrawDone;
use App\Mail as MailModel;
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

        event(new DrawDone($draw));
    }

    protected function createDraw($title, $body, $dataExpiration): Draw
    {
        $draw = new Draw();
        $draw->expires_at = $dataExpiration;
        $draw->mail_title = $title;
        $draw->mail_body = $body;
        $draw->save();

        return $draw;
    }

    protected function createParticipants(Draw $draw, array $participants, $hat): void
    {
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
            $draw->participants[$santaIdx]->exclusions = array_map(function ($participantId) use ($participants) {
                return $participants[$participantId]->id;
            }, $participants[$santaIdx]->exclusions);
            $draw->participants[$santaIdx]->target()->save($participants[$targetIdx]);
            $draw->participants[$santaIdx]->save();
        }
    }
}
