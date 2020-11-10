<?php

namespace App\Jobs;

use App\Models\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mail;
use DrawCrypt;

class SendMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $participant;
    protected $mailable;
    protected $key;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Participant $participant, Mailable $mailable)
    {
        $this->participant = $participant;
        $this->mailable = $mailable;
        $this->key = base64_encode(DrawCrypt::getKey());
    }

    public function getRecipient()
    {
        return $this->participant;
    }

    public function getMailable()
    {
        return $this->mailable;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DrawCrypt::setKey(base64_decode($this->key));
        Mail::to($this->participant)->send($this->mailable);
    }
}
