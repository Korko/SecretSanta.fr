<?php

namespace App\Services;

use App\Models\Draw;
use App\Models\Participant;
use App\Models\PendingDraw;
use DB;
use Illuminate\Support\Arr;
use Throwable;

class DrawFormHandler
{
    protected $title;

    protected $body;

    protected $organizer;

    protected $participants;

    public function __construct()
    {
        $this->title = '';
        $this->body = '';
        $this->organizer = null;
        $this->participants = [];
    }

    public function handle(PendingDraw $pending): Draw
    {
        if (! Arr::get($pending->data, 'participant-organizer', false)) {
            $this->withOrganizer($pending->data['organizer']);
        }

        return $this
            ->withParticipants($pending->data['participants'])
            ->withTitle($pending->data['title'])
            ->withBody($pending->data['content'])
            ->save();
    }

    protected function withOrganizer(array $organizer): self
    {
        $this->organizer = $organizer;

        return $this;
    }

    protected function withParticipants(array $participants): self
    {
        $this->participants = $participants;
        if (! isset($this->organizer)) {
            $this->organizer = current($participants);
            unset($this->organizer['exclusions']);
        }

        return $this;
    }

    protected function withTitle($title): self
    {
        $this->title = $title;

        return $this;
    }

    protected function withBody($body): self
    {
        $this->body = $body;

        return $this;
    }

    protected function save(): Draw
    {
        DB::beginTransaction();

        $draw = $this->createDraw();

        try {
            $this->solveExclusions($draw);

            DrawHandler::solve($draw);

            DB::commit();

            return $draw;
        } catch (Throwable $e) {
            DB::rollBack();

            throw $e;
        }
    }

    protected function createDraw(): Draw
    {
        $draw = new Draw();
        $draw->mail_title = $this->title;
        $draw->mail_body = $this->body;
        $draw->organizer_name = $this->organizer['name'];
        $draw->organizer_email = $this->organizer['email'];
        $draw->save();

        foreach ($this->participants as $idx => $santa) {
            $participant = new Participant();
            $participant->draw()->associate($draw);
            $participant->name = $santa['name'];
            $participant->email = Arr::get($santa, 'email');
            $participant->save();

            $this->participants[$idx]['model'] = $participant;
            $draw->participants()->save($participant);
        }

        return $draw;
    }

    protected function solveExclusions(Draw $draw): void
    {
        $participants = $this->participants;
        for ($i = 0; $i < count($participants); $i++) {
            $participant = $participants[$i];

            $participant['model']->exclusions()->attach(
                array_map(function ($participantId) use ($participants) {
                    return $participants[intval($participantId)]['model']->id;
                }, Arr::get($participant, 'exclusions', []))
            );
        }
    }
}
