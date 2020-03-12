<?php

namespace App\Mail;

use App\Mail as MailModel;
use Hashids;

class TrackedMailable extends Mailable
{
    protected $mailId;
    protected $updateDatetime;

    protected function track(MailModel $mailModel)
    {
        $this->mailId = $mailModel->id;
        $this->updateDatetime = $mailModel->updated_at;
    }

    protected function updateDeliveryStatus($status)
    {
        if (isset($this->mailId)) {
            $mailModel = MailModel::find($this->mailId);

            if ($mailModel->updated_at == $this->updateDatetime) {
                $mailModel->updateDeliveryStatus($status);
            }
        }
    }

    public function success()
    {
        $this->updateDeliveryStatus(MailModel::SENT);
    }

    public function failed()
    {
        $this->updateDeliveryStatus(MailModel::ERROR);
    }

    public function send($mailer)
    {
        $this->withSwiftMessage(function ($message) {
            $hash = Hashids::connection('bounce')->encode($this->mailId);

            $message->getHeaders()
                    ->addPathHeader('Return-Path', str_replace('*', $hash, config('mail.return_path')));
        });

        return parent::send($mailer);
    }
}
