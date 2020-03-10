<?php

namespace App\Jobs;

use App\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $participant;
    protected $mailable;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Participant $participant, Mailable $mailable)
    {
        $this->participant = $participant;
        $this->mailable = $mailable;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to([['email' => $this->participant->address, 'name' => $this->participant->name]])
            ->send($this->mailable);
    }
}
