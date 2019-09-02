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
use Mail;
use Metrics;
use Sms;

class TargetDrawn extends Notification
{
    use Queueable;

    private $santa;
    private $target;
    private $superSanta;
    private $participants;
    private $participant;
    private $mailContent;
    private $dearSanta;

    public function __construct(array $santa, array $target, array $superSanta, array $participants, Participant $participant, array $mailContent, array $smsContent, $dearSanta = false)
    {
        $this->santa = $santa;
        $this->target = $target;
        $this->superSanta = $superSanta;
        $this->participants = $participants;
        $this->participant = $participant;
        $this->mailContent = $mailContent;
        $this->smsContent = $smsContent;
        $this->dearSanta = $dearSanta;
    }

    public function via(): array
    {
        $channels = [];

        if (!empty($this->mailContent) and !empty($this->santa['email'])) {
            $channels[] = 'mail';
        }

        if (!empty($this->smsContent) and !empty($this->santa['phone'])) {
            $channels[] = 'sms';
        }

        return $channels;
    }

    public function toMail(): Mailable
    {
        $dearSantaLink = null;
        if ($this->dearSanta) {
            $dearSantaLink = $this->getDearSantaLink($this->participant->draw, $this->superSanta);
        }

        Metrics::increment('email');

        return (new TargetDrawnEmail($this->participant->draw, $this->mailContent['title'], $this->mailContent['body'], $this->santa['name'], $this->target['name'], $dearSantaLink))
            ->withEventData([
                'participant' => $this->participant,
            ]);
    }

    protected function getDearSantaLink(Draw $draw, array $santa)
    {
        $santaSymKey = SymmetricalEncrypter::generateKey(config('app.cipher'));

        $dearSanta = DearSanta::prepareAndSave($draw, $santa, $santaSymKey);

        return route('dearsanta', ['santa' => Hashids::encode($dearSanta->id)]).'#'.base64_encode($santaSymKey);
    }

    public function toSMS()
    {
        Metrics::increment('phone');
        Metrics::increment('sms', SmsTools::count($this->smsBody));

        $contentSms = str_replace(['{SANTA}', '{TARGET}'], [$this->santa['name'], $this->target['name']], $this->smsBody);

        Sms::message($this->santa['phone'], $contentSms);
    }
}
