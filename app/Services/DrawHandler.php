<?php

namespace App\Services;

use App\DearSanta;
use App\DearSantaDraw;
use App\Draw;
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
    private $asymKeys;

    private $dearSantaDraw;

    public function __construct()
    {
        $this->symKey = SymmetricalEncrypter::generateKey(config('app.cipher'));
        $this->asymKeys = AsymmetricalEncrypter::generateKeys(1024);
    }

    public function contactParticipants(array $participants, array $hat, array $mailContent, array $smsContent, $dataExpiration = null)
    {
        $dearSantaDraw = new DearSantaDraw();
        $dearSantaDraw->expiration = $dataExpiration;
        $dearSantaDraw->save();

        $this->informOrganizer($dearSantaDraw, $participants[0], $mailContent['title']);

        if (!empty($mailContent) and !empty(array_filter(array_column($participants, 'email')))) {
            $this->sendMails($participants, $hat, $mailContent, $dataExpiration);
        }

        if (!empty($smsContent) and !empty(array_filter(array_column($participants, 'phone')))) {
            $this->sendSMSs($participants, $hat, $smsContent['body']);
        }
    }

    public function informOrganizer(Draw $draw, $organizer, $title)
    {
        $organizerPanelLink = $this->getOrganizerLink($draw);

        Mail::to($organizer['email'], $organizer['name'])
            ->send(
                (new Organizer($title, $organizerPanelLink))
            );
    }

    protected function getOrganizerPanelLink(Draw $draw)
    {
        return route('organizerPanel', ['draw' => Hashids::encode($draw->id)]).'#'.base64_encode($this->symKey);
    }

    protected function sendMails(array $participants, array $hat, array $mailContent, $dataExpiration = null)
    {
        $dearSantaDraw = null;
        if ($dataExpiration !== null) {
            $dearSantaDraw = new DearSantaDraw();
            $dearSantaDraw->expiration = $dataExpiration;
            $dearSantaDraw->save();
        }

        $draw = Draw::prepareAndSave($mailContent, $dataExpiration, $participants[0], $this->symKey, $dearSantaDraw);

        foreach ($hat as $santaIdx => $targetIdx) {
            $santa = $participants[$santaIdx];
            $target = $participants[$targetIdx];

            if (!empty($santa['email'])) {
                // Santa of that santa
                $superSanta = $participants[array_search($santaIdx, $hat)];

                $dearSantaLink = null;
                if ($dearSantaDraw !== null) {
                    $dearSantaLink = $this->getDearSantaLink($dearSantaDraw, $superSanta);
                }

                $participant = Participant::prepareAndSave($draw, $santa, $this->symKey, $dearSantaLink);

                $this->sendSingleMail($mailContent, $santa, $target, $participant, $dearSantaLink);
            }
        }
    }

    protected function sendSingleMail(array $mailContent, array $santa, array $target, Participant $participant, $dearSantaLink = null)
    {
        $substitutions = [
            '{SANTA}'  => $santa['name'],
            '{TARGET}' => $target['name'],
        ];

        Metrics::increment('email');

        Mail::to($santa['email'], $santa['name'])
            ->send(
                (new TargetDrawn($mailContent['title'], $mailContent['body'], $dearSantaLink))
                    ->withSubstitutions($substitutions)
                    ->withEventData([
                        'participant' => $participant,
                    ])
            );
    }

    protected function sendSMSs(array $participants, array $hat, $smsBody)
    {
        foreach ($hat as $santaIdx => $targetIdx) {
            $santa = $participants[$santaIdx];
            $target = $participants[$targetIdx];

            if (!empty($santa['phone'])) {
                $this->sendSingleSms($smsBody, $santa, $target);
            }
        }
    }

    protected function sendSingleSms($smsBody, array $santa, array $target)
    {
        Metrics::increment('phone');
        Metrics::increment('sms', SmsTools::count($smsBody));

        $contentSms = str_replace(['{SANTA}', '{TARGET}'], [$santa['name'], $target['name']], $smsBody);
        Sms::message($santa['phone'], $contentSms);
    }

    protected function getDearSantaLink(DearSantaDraw $dearSantaDraw, array $santa)
    {
        $dearSanta = DearSanta::prepareAndSave($dearSantaDraw, $santa, $this->asymKeys['public']);

        return route('dearsanta', ['santa' => Hashids::encode($dearSanta->id)]).'#'.base64_encode($this->asymKeys['private']);
    }
}
