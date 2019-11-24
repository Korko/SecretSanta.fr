<?php

namespace App\Services;

use Arr;
use Sms;
use Mail;
use Metrics;
use Hashids;
use App\Draw;
use App\DearSanta;
use App\Participant;
use App\Mail\TargetDrawn;
use App\Mail\OrganizerRecap;
use Facades\App\Services\SmsTools as SmsTools;

class DrawHandler
{
    public function contactParticipants(array $participants, array $hat, array $mailContent, array $smsContent, $dataExpiration, $dearSanta = false)
    {
        $draw = new Draw();
        $draw->expires_at = $dataExpiration;
        $draw->email_title = $mailContent['title'];
        $draw->email_body = $mailContent['body'];
        $draw->sms_body = $smsContent['body'];
        $draw->dear_santa = $dearSanta;
        $draw->save();

        foreach ($hat as $santaIdx => $targetIdx) {
            $santa = ['id' => $santaIdx] + $participants[$santaIdx];
            $target = ['id' => $targetIdx] + $participants[$targetIdx];

            $participant = new Participant();
            $participant->setEncryptionKey($draw->getEncryptionKey()); // Have to be very first attribute set
            $participant->draw_id = $draw->id;
            $participant->name = $santa['name'];
            $participant->email_address = Arr::get($santa, 'email');
            $participant->phone_number = Arr::get($santa, 'phone');
            $participant->target = $target;
            $participant->save();

            $superSanta = $participants[array_search($santa['id'], $hat)];

            if ($santaIdx === 0) {
                $this->informOrganizer($draw, $participant);
            }

            $this->informParticipant($draw, $participant, $superSanta);
        }
    }

    public function informOrganizer(Draw $draw, Participant $organizer)
    {
        $panelLink = route('organizerPanel', ['draw' => $organizer->draw_id]).'#'.base64_encode($organizer->getEncryptionKey());

        Mail::to([['email' => $organizer->email_address, 'name' => $organizer->name]])
            ->send(new OrganizerRecap($organizer->draw, $panelLink));
    }

    public function informParticipant(Draw $draw, Participant $participant, array $superSanta)
    {
        if (! empty($draw->email_body) and ! empty($participant->email_address)) {
            $this->sendEmail($draw, $participant, $superSanta);
        }

        if (! empty($draw->sms_body) and ! empty($participant->phone_number)) {
            $this->sendSMS($draw, $participant);
        }
    }

    public function sendEmail(Draw $draw, Participant $participant, array $superSanta)
    {
        $dearSantaLink = null;
        if ($draw->dear_santa) {
            $dearSantaLink = $this->getDearSantaLink($draw, $superSanta);
        }

        Metrics::increment('email');

        Mail::to([['email' => $participant->email_address, 'name' => $participant->name]])
            ->send(
                (new TargetDrawn($draw, $participant, $dearSantaLink))
                    ->withEventData([
                        'participant' => $participant,
                    ])
            );
    }

    protected function getDearSantaLink(Draw $draw, array $santa)
    {
        $dearSanta = new DearSanta();
        $dearSanta->draw_id = $draw->id;
        $dearSanta->santa_name = $santa['name'];
        $dearSanta->santa_email = $santa['email'];
        $dearSanta->save();

        return route('dearsanta', ['santa' => Hashids::encode($dearSanta->id)]).'#'.base64_encode($dearSanta->getEncryptionKey());
    }

    protected function sendSMS(Draw $draw, Participant $participant)
    {
        Metrics::increment('phone');
        Metrics::increment('sms', SmsTools::count($draw->sms_body));

        $contentSms = str_replace(['{SANTA}', '{TARGET}'], [$participant->name, $participant->target->name], $draw->sms_body);

        Sms::message($participant->phone_number, $contentSms);
    }
}

