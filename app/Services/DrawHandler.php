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
    public function contactParticipants(array $participants, array $hat, array $mailContent, array $smsContent, $dataExpiration, $dearSanta = false)
    {
        $orgaSymKey = SymmetricalEncrypter::generateKey(config('app.cipher'));

        $draw = Draw::prepareAndSave($mailContent, $dataExpiration, $participants[0], $orgaSymKey, $dearSanta);

        $this->informOrganizer($draw, $participants[0], $mailContent['title'], $orgaSymKey);

        foreach ($hat as $santaIdx => $targetIdx) {
            $santa = ['id' => $santaIdx] + $participants[$santaIdx];
            $target = ['id' => $targetIdx] + $participants[$targetIdx];

            $participant = Participant::prepareAndSave($draw, $santa, $target, $orgaSymKey);

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

    public function informOrganizer(Draw $draw, $organizer, $title, $orgaSymKey)
    {
        $organizerPanelLink = $this->getOrganizerPanelLink($draw, $orgaSymKey);

        Mail::to([$organizer])
            ->send(new OrganizerEmail($organizerPanelLink));
    }

    protected function getOrganizerPanelLink(Draw $draw, $orgaSymKey)
    {
        return route('organizerPanel', ['draw' => Hashids::encode($draw->id)]).'#'.base64_encode($orgaSymKey);
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
        $santaSymKey = SymmetricalEncrypter::generateKey(config('app.cipher'));

        $dearSanta = DearSanta::prepareAndSave($draw, $santa, $santaSymKey);

        return route('dearsanta', ['santa' => Hashids::encode($dearSanta->id)]).'#'.base64_encode($santaSymKey);
    }
}
