<?php

namespace App\Services;

use App\DearSanta;
use App\Draw;
use App\Mail\Organizer as OrganizerEmail;
use App\Mail\TargetDrawn;
use App\Participant;
use Facades\App\Services\SmsTools as SmsTools;
use Hashids;
use Mail;
use Metrics;
use Sms;

class DrawHandler
{
    private $symKey;

    public function __construct()
    {
        $this->symKey = SymmetricalEncrypter::generateKey(config('app.cipher'));
    }

    public function contactParticipants(array $participants, array $hat, array $mailContent, array $smsContent, $dataExpiration, $dearSanta = false)
    {
        $draw = Draw::prepareAndSave($mailContent, $dataExpiration, $participants[0], $this->symKey, $dearSanta);

        $this->informOrganizer($draw, $participants[0], $mailContent['title']);

        foreach ($hat as $santaIdx => $targetIdx) {
            $santa = ['id' => $santaIdx] + $participants[$santaIdx];
            $target = ['id' => $targetIdx] + $participants[$targetIdx];

            $participant = Participant::prepareAndSave($draw, $santa, $target, $this->symKey);

            if (!empty($mailContent) and !empty($santa['email'])) {
                // Santa of that santa
                $superSanta = $participants[array_search($santa['id'], $hat)];

                $this->sendMail($santa, $target, $superSanta, $participant, $participants, $mailContent, $dearSanta);
            }

            if (!empty($smsContent) and !empty($santa['phone'])) {
                $this->sendSMS($santa, $target, $participants, $smsContent['body']);
            }
        }
    }

    public function informOrganizer(Draw $draw, $organizer, $title)
    {
        $organizerPanelLink = $this->getOrganizerPanelLink($draw);

        Mail::to([$organizer])
            ->send(new OrganizerEmail($title, $organizerPanelLink));
    }

    protected function getOrganizerPanelLink(Draw $draw)
    {
        return route('organizerPanel', ['draw' => Hashids::encode($draw->id)]).'#'.base64_encode($this->symKey);
    }

    protected function sendMail(array $santa, array $target, array $superSanta, Participant $participant, array $participants, array $mailContent, $dearSanta = false)
    {
        $dearSantaLink = null;
        if ($dearSanta) {
            $dearSantaLink = $this->getDearSantaLink($participant->draw, $superSanta);
        }

        $substitutions = [
            '{SANTA}'  => $santa['name'],
            '{TARGET}' => $target['name'],
        ];

        Metrics::increment('email');

        Mail::to([$santa])
            ->send(
                (new TargetDrawn($mailContent['title'], $mailContent['body'], $dearSantaLink))
                    ->withSubstitutions($substitutions)
                    ->withEventData([
                        'participant' => $participant,
                    ])
            );
    }

    protected function sendSMS(array $santa, array $target, array $participants, $smsBody)
    {
        Metrics::increment('phone');
        Metrics::increment('sms', SmsTools::count($smsBody));

        $contentSms = str_replace(['{SANTA}', '{TARGET}'], [$santa['name'], $target['name']], $smsBody);

        Sms::message($santa['phone'], $contentSms);
    }

    protected function getDearSantaLink(Draw $draw, array $santa)
    {
        $dearSanta = DearSanta::prepareAndSave($draw, $santa, $this->symKey);

        return route('dearsanta', ['santa' => Hashids::encode($dearSanta->id)]).'#'.base64_encode($this->symKey);
    }
}
