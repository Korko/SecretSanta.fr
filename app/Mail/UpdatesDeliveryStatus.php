<?php

namespace App\Mail;

use App\Mail;
use Illuminate\Queue\SerializesModels;

trait UpdatesDeliveryStatus
{
    use SerializesModels;

    private $mail;
    private $updateDatetime;

    public function trackMail(Mail $mail)
    {
        $this->mail = $mail;
        $this->updateDatetime = $mail->updated_at;
    }

    protected function getMailId()
    {
        return $this->mail->id;
    }

    public function send($mailer)
    {
        parent::send($mailer);

        // Models are fetched again once the task is runned
        // So the update time may have changed inbetween
        // In that case, ignore update for this task
        if ($this->mail->updated_at == $this->updateDatetime) {
            $this->mail->delivery_status = Mail::SENT;
            $this->mail->save();
        }
    }

    public function failed($exception)
    {
        if ($this->mail->updated_at == $this->updateDatetime) {
            $this->mail->delivery_status = Mail::ERROR;
            $this->mail->save();
        }
    }
}
