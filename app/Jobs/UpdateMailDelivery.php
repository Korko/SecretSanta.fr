<?php

namespace App\Jobs;

use App\Models\Mail as MailModel;
use App\Traits\UpdatesMailDelivery;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdateMailDelivery implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels, UpdatesMailDelivery;

    protected $status;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(MailModel $mail, $status)
    {
        $this->store($mail);

        $this->status = $status;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->delayedUpdateDelivery($this->status);
    }
}
