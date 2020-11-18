<?php

namespace App\Traits;

use App\Events\MailStatusUpdated;
use App\Models\Mail as MailModel;

trait UpdatesMailDelivery
{
    public $mailId;
    public $version;

    public function store(MailModel $mail)
    {
        $this->mailId = $mail->id;
        $this->version = $mail->version;
    }

    public function delayedUpdateDelivery($status)
    {
        if (isset($this->mailId)) {
            $mail = MailModel::find($this->mailId);

            return $this->updateDelivery($mail, $status, $this->version);
        }
    }

    public function updateDelivery(MailModel $mail, $status, $version = null)
    {
        if (! isset($version) or $mail->version === (int)$version) {
            $mail->updateDeliveryStatus($status);

            event(new MailStatusUpdated($mail));
        }

        return $mail;
    }
}