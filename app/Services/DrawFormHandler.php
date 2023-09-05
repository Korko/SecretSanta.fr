<?php

namespace App\Services;

use App\Models\Draw;
use App\Models\Participant;
use DB;
use Illuminate\Support\Arr;

class DrawFormHandler
{
    protected $title;

    protected $body;

    protected $organizer;

    protected $participants;

    public function handle(Draw $draw): Draw
    {
        return DB::transaction(function () use ($draw): Draw {
            /*$draw = $this->createDraw(
                organizer: $draw->organizer(),
                participants: $draw->participants(),
            );*/

            DrawHandler::solve($draw);

            return $draw;
        });
    }

    /**
     * If organizer is null, then it's the first participant. If defined, it's not a participant.
     *
     * @param  array|null  $organizer
     * @param  array  $participants
     * @return Draw
     */
    protected function createDraw(array $organizer = null, array $participants): Draw
    {
        foreach ($participants as $participant) {
            // TODO
            // $participant->setTransientAttribute('exclusions', Arr::get($santa, 'exclusions', []));
        }

        // Don't use $draw->participants or you'll love the transient attributes
        foreach ($participants as $participant) {
            // TODO
            // $participant->exclusions()->attach(
                // exclusions is 0 based request form indexed, we need 1 based sql table index instead
            //     array_map(function ($participantId) use ($participants) {
            //         return $participants[intval($participantId)]->id;
            //     }, $participant->getTransientAttribute('exclusions', []))
            // );
        }

        return $draw;
    }
}
