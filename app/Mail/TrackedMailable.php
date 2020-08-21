<?php

namespace App\Models\Mail;

use App\Models\Mail as MailModel;

class TrackedMailable extends Mailable
{
    public $mailId;
    public $updateDatetime;
    public $returnPath;

    protected function track(MailModel $mailModel)
    {
        $this->mailId = $mailModel->id;
        $this->updateDatetime = $mailModel->updated_at;
        $this->returnPath = str_replace('*', $mailModel->hash, config('mail.return_path'));
    }

    protected function updateDeliveryStatus($status)
    {
        if (isset($this->mailId)) {
            $mailModel = MailModel::find($this->mailId);

            if ($mailModel->updated_at->equalTo($this->updateDatetime)) {
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

    /**
     * Define the return-path in case of bounce
     * and send the mail
     *
     * @param  \Illuminate\Contracts\Mail\Factory|\Illuminate\Contracts\Mail\Mailer  $mailer
     * @return void
     */
    public function send($mailer): void
    {
        $this->withSwiftMessage(function ($message) {
            $message
                ->getHeaders()
                ->addPathHeader('Return-Path', $this->returnPath);
        });

        parent::send($mailer);
    }

}
