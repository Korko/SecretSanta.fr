<?php

namespace App\Mail;

use App\Database\Model;
use Illuminate\Contracts\Mail\Mailer as MailerContract;
use Illuminate\Queue\SerializesModels;

trait UpdatesDeliveryStatus
{
    use SerializesModels;

    private $entry;
    private $updateDatetime;

    public function trackEntry(Model $entry)
    {
        $this->entry = $entry;
        $this->updateDatetime = $entry->updated_at;
    }

    public function send(MailerContract $mailer)
    {
        parent::send($mailer);

        // Models are fetched again once the task is runned
        // So the update time may have changed inbetween
        // In that case, ignore update for this task
        if ($this->entry->updated_at == $this->updateDatetime) {
            $this->entry->delivery_status = $this->entry::SENT;
            $this->entry->save();
        }
    }

    public function failed($exception)
    {
        if ($this->entry->updated_at == $this->updateDatetime) {
            $this->entry->delivery_status = $this->entry::ERROR;
            $this->entry->save();
        }
    }
}
