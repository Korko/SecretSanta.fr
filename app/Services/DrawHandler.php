<?php

namespace App\Services;

use App\Exceptions\SolverException;
use App\Models\Draw;
use App\Models\Mail as MailModel;
use App\Models\Participant;
use Arr;
use DB;

class DrawHandler
{
    public $title;
    public $body;
    public $participants;
    public $expirationDate;

    public function __construct()
    {
        $this->title = '';
        $this->body = '';
        $this->participants = [];
        $this->expirationDate = date('Y-m-d', strtotime('+2 days'));
    }

    public function withParticipants(array $participants) : self
    {
        $this->participants = $participants;

        return $this;
    }

    public function withTitle($title) : self
    {
        $this->title = $title;

        return $this;
    }

    public function withBody($body) : self
    {
        $this->body = $body;

        return $this;
    }

    public function withExpiration($expirationDate) : self
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    public function save() : Draw
    {
        DB::beginTransaction();

        $draw = $this->createDraw();

        try {
            $this->solveExclusions($draw);

            $draw->next_solvable = $draw->participants->canRedraw();
            $draw->save();

            DB::commit();

            return $draw;
        } catch(SolverException $e) {
            DB::rollBack();

            throw $e;
        }
    }

    protected function createDraw() : Draw
    {
        $draw = new Draw();
        $draw->expires_at = $this->expirationDate;
        $draw->mail_title = $this->title;
        $draw->mail_body = $this->body;
        $draw->save();

        foreach ($this->participants as $idx => $santa) {
            $mail = (new MailModel())->draw()->associate($draw);
            $mail->save();

            $participant = new Participant();
            $participant->draw()->associate($draw);
            $participant->name = $santa['name'];
            $participant->email = Arr::get($santa, 'email');
            $participant->mail()->associate($mail);
            $participant->save();

            $this->participants[$idx]['model'] = $participant;
            $draw->participants()->save($participant);
        }

        return $draw;
    }

    protected function solveExclusions(Draw $draw) : void
    {
        $participants = $this->participants;
        for ($i = 0; $i < count($participants); $i++) {
            $participant = $participants[$i];

            foreach ($participant['exclusions'] as $participantId) {
                $participant['model']->exclusions()->attach($participants[intval($participantId)]['model']->id);
            }
        }

        $hat = $draw->participants->solve();
        $participants = $draw->participants->pluck(null, 'id');
        foreach ($hat as $santaIdx => $targetIdx) {
            $participants[$santaIdx]->target()->save($participants[$targetIdx]);
        }
    }
}