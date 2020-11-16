<?php

namespace App\Jobs;

use App\Mail\TrackedMailable;
use App\Models\Mail as MailModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ValidateEmailDelivery implements ShouldQueue
{
    use Dispatchable, SerializesModels, Queueable;

    protected $mailable;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(TrackedMailable $mailable)
    {
        $this->mailable = $mailable;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->mailable->updateDeliveryStatus(MailModel::SENT);
    }
}
