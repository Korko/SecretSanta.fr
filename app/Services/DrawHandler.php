<?php

namespace App\Services;

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

    public function __construct()
    {
        $this->symKey = SymmetricalEncrypter::generateKey(config('app.cipher'));
        $this->asymKeys = AsymmetricalEncrypter::generateKeys(1024);
    }

    public function contactParticipants(array $participants, array $hat, array $mailContent, array $smsContent, $dearSantaExpiration = null)
    {
        if (!empty($mailContent)) {
            $this->sendMails($participants, $hat, $mailContent, $dearSantaExpiration);
        }

        if (!empty($smsContent)) {
            $this->sendSMSs($participants, $hat, $smsContent['body']);
        }
    }

    protected function sendMails(array $participants, array $hat, array $mailContent, $dearSantaExpiration = null)
    {
        $this->draw = Draw::prepareAndSave($mailContent, $dearSantaExpiration, $participants[0], $this->symKey);

        foreach ($hat as $santaIdx => $targetIdx) {
            $santa = $participants[$santaIdx];
            $target = $participants[$targetIdx];

            if (!empty($santa['email'])) {
                // Santa of that santa
                $superSanta = $participants[array_search($santaIdx, $hat)];

                $dearSantaLink = null;
                if ($dearSantaExpiration !== null) {
                    $dearSantaLink = $this->getDearSantaLink($superSanta);
                }

                $this->sendSingleMail($mailContent, $santa, $target, $dearSantaLink);
            }
        }
    }

    protected function sendSingleMail(array $mailContent, array $santa, array $target, $dearSantaLink = null)
    {
        $substitutions = [
            '{SANTA}'  => $santa['name'],
            '{TARGET}' => $target['name'],
            ':link'    => $dearSantaLink,
        ];

        Metrics::increment('email');

        Mail::to($santa['email'], $santa['name'])
            ->send((new TargetDrawn($mailContent['title'], $mailContent['body']))->withSubstitutions($substitutions));
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

    protected function getDearSantaLink(array $santa)
    {
        $participant = Participant::prepareAndSave($this->draw, $santa, $this->asymKeys['public']);

        return route('dearsanta', ['santa' => Hashids::encode($participant->id)]).'#'.base64_encode($this->asymKeys['private']);
    }
}
