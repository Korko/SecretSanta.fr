<?php

namespace App\Services;

use Arr;
use Mail;
use Crypt;
use Metrics;
use App\Draw;
use App\Participant;
use App\Mail\TargetDrawn;
use App\Mail\OrganizerRecap;

class DrawHandler
{
    public function contactParticipants(array $participants, array $hat, array $mailContent, $dataExpiration)
    {
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
            $participant->save();

            $participants[$idx] = $participant;
            $draw->participants->add($participant);
        }

        foreach ($hat as $santaIdx => $targetIdx) {
            $participants[$santaIdx]->target()->save($participants[$targetIdx]);
            $participants[$santaIdx]->save();
        }

        $this->informOrganizer($draw);
        $this->informParticipants($draw);
    }

    public function informOrganizer(Draw $draw)
    {
        $panelLink = route('organizerPanel', ['draw' => $draw->id]).'#'.base64_encode(Crypt::getKey());

        Mail::to([['email' => $draw->organizer->email_address, 'name' => $draw->organizer->name]])
            ->queue(new OrganizerRecap($draw, $panelLink));
    }

    public function informParticipants(Draw $draw)
    {
        foreach ($draw->participants as $participant) {
            Metrics::increment('email');

            Mail::to([['email' => $participant->email_address, 'name' => $participant->name]])
                ->queue(new TargetDrawn($participant));

        }
    }
}
