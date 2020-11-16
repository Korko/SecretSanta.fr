<?php

namespace App\Mail;

use App\Events\MailStatusUpdated;
use App\Jobs\ValidateEmailDelivery;
use App\Models\Mail as MailModel;
use Illuminate\Foundation\Bus\DispatchesJobs;

class TrackedMailable extends Mailable
{
    use DispatchesJobs;

    public $mailId;
    public $updateDatetime;
    public $returnPath;

    protected function track(MailModel $mailModel)
    {
        $this->mailId = $mailModel->id;
        $this->updateDatetime = $mailModel->updated_at;
        $this->returnPath = str_replace('*', $mailModel->hash, config('mail.return_path'));
    }

    public function onMailUpdate(MailModel $mail)
    {
        return null;
    }

    public function updateDeliveryStatus($status)
    {
        if (isset($this->mailId)) {
            $mailModel = MailModel::find($this->mailId);

            if ($mailModel->updated_at->equalTo($this->updateDatetime)) {
                $mailModel->updateDeliveryStatus($status);

                $this->updateDatetime = $mailModel->updated_at;
            }

            $this->onMailUpdate($mailModel);
        }
    }

    public function success()
    {
        $this->updateDeliveryStatus(MailModel::SENDING);

        // Cannot serialize with callbacks
        // We won't need those
        $this->callbacks = [];

        $job = (new ValidateEmailDelivery($this))->delay(10);
        $this->dispatch($job);
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