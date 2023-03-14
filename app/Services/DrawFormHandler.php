<?php

namespace App\Services;

use App\Models\Draw;
use App\Models\Participant;
use App\Models\PendingDraw;
use DB;
use Illuminate\Support\Arr;

class DrawFormHandler
{
    protected $title;

    protected $body;

    protected $organizer;

    protected $participants;

    public function handle(PendingDraw $pending): Draw
    {
        if (! Arr::get($pending->data, 'participant-organizer', false)) {
            $organizer = $pending->data['organizer'];
        } else {
            $organizer = null;
        }

        return DB::transaction(function () use ($organizer, $pending): Draw {
            $draw = $this->createDraw(
                organizer: $organizer,
                participants: $pending->data['participants'],
                title: $pending->data['title'],
                body: $pending->data['content']
            );

            DrawHandler::solve($draw);

            return $draw;
        });
    }

    /**
     * If organizer is null, then it's the first participant. If defined, it's not a participant.
     *
     * @param  array|null  $organizer
     * @param  array  $participants
     * @param  string  $title
     * @param  string  $body
     * @return Draw
     */
    protected function createDraw(array $organizer = null, array $participants, string $title, string $body): Draw
    {
        $draw = new Draw();
        $draw->mail_title = $title;
        $draw->mail_body = $body;
        $draw->organizer_participant = is_null($organizer);
        $draw->organizer_name = $organizer['name'] ?? current($participants)['name'];
        $draw->organizer_email = $organizer['email'] ?? current($participants)['email'];
        $draw->save();

        foreach ($participants as $idx => $santa) {
            $participant = new Participant();
            $participant->draw()->associate($draw);
            $participant->name = $santa['name'];
            $participant->email = Arr::get($santa, 'email');
            $participant->setTransientAttribute('exclusions', Arr::get($santa, 'exclusions', []));

            $participants[$idx] = $participant;
        }

        $draw->participants()->saveMany($participants);

        // Don't use $draw->participants or you'll love the transient attributes
        foreach ($participants as $participant) {
            $participant->exclusions()->attach(
                // exclusions is 0 based request form indexed, we need 1 based sql table index instead
                array_map(function ($participantId) use ($participants) {
                    return $participants[intval($participantId)]->id;
                }, $participant->getTransientAttribute('exclusions', []))
            );
        }

        return $draw;
    }
}
