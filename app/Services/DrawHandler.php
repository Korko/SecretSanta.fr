<?php

namespace App\Services;

use Arr;
use Sms;
use Mail;
use Crypt;
use Metrics;
use App\Draw;
use App\Participant;
use App\Mail\TargetDrawn;
use App\Mail\OrganizerRecap;
use Facades\App\Services\SmsTools as SmsTools;

class DrawHandler
{
    public function contactParticipants(array $participants, array $hat, array $mailContent, array $smsContent, $dataExpiration)
    {
        $draw = new Draw();
        $draw->expires_at = $dataExpiration;
        $draw->email_title = $mailContent['title'];
        $draw->email_body = $mailContent['body'];
        $draw->sms_body = $smsContent['body'];
        $draw->save();

        // We wont store the phone number in DB
        // But we want to keep it in the instance
        $draw->participants = collect();
        foreach ($participants as $idx => $santa) {
            $participant = new Participant();
            $participant->draw()->associate($draw);
            $participant->name = $santa['name'];
            $participant->email_address = Arr::get($santa, 'email');
            $participant->phone_number = Arr::get($santa, 'phone');
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
            if (! empty($draw->email_body) and ! empty($participant->email_address)) {
                $this->sendEmail($participant);
            }

            if (! empty($draw->sms_body) and ! empty($participant->phone_number)) {
                $this->sendSMS($participant);
            }
        }
    }

    public function sendEmail(Participant $participant)
    {
        Metrics::increment('email');

        Mail::to([['email' => $participant->email_address, 'name' => $participant->name]])
            ->queue(new TargetDrawn($participant));
    }

    protected function sendSMS(Participant $participant)
    {
        Metrics::increment('phone');
        Metrics::increment('sms', SmsTools::count($participant->draw->sms_body));

        $contentSms = str_replace(['{SANTA}', '{TARGET}'], [$participant->name, $participant->target->name], $participant->draw->sms_body);

        Sms::message($participant->phone_number, $contentSms);
    }
}
