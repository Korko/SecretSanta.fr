<?php

namespace App\Notifications;

use App\DearSanta;
use App\Draw;
use App\Mail\TargetDrawn as TargetDrawnEmail;
use App\Participant;
use App\Services\SymmetricalEncrypter;
use Hashids;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;
use Metrics;
use Sms;

class TargetDrawn extends Notification
{
    use Queueable;

    private $draw;
    private $superSanta;

    public function __construct(Draw $draw, array $superSanta)
    {
        $this->draw = $draw;
        $this->superSanta = $superSanta;
    }

    public function via(Participant $participant): array
    {
        $channels = [];

        if (!empty($this->draw->email_body) and !empty($participant->email_address)) {
            $channels[] = 'mail';
        }

        if (!empty($this->draw->sms_body) and !empty($participant->phone_number)) {
            $channels[] = 'sms';
        }

        return $channels;
    }

    public function toMail(Participant $participant): Mailable
    {
        $dearSantaLink = null;
        if ($this->draw->dear_santa) {
            $dearSantaLink = $this->getDearSantaLink($this->draw, $this->superSanta);
        }

        Metrics::increment('email');

        return (new TargetDrawnEmail($this->draw, $participant, $dearSantaLink))
            ->withEventData([
                'participant' => $participant,
            ]);
    }

    protected function getDearSantaLink(Draw $draw, array $santa)
    {
        $santaSymKey = SymmetricalEncrypter::generateKey(config('app.cipher'));

        $dearSanta = DearSanta::prepareAndSave($draw, $santa, $santaSymKey);

        return route('dearsanta', ['santa' => Hashids::encode($dearSanta->id)]).'#'.base64_encode($santaSymKey);
    }

    public function toSMS(Participant $participant)
    {
        Metrics::increment('phone');
        Metrics::increment('sms', SmsTools::count($this->draw->sms_body));

        $contentSms = str_replace(['{SANTA}', '{TARGET}'], [$participant->name, $participant->target->name], $this->draw->sms_body);

        Sms::message($participant->phone_number, $contentSms);
    }
}
