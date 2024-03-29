<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Mail\Message;
use Mail;

class SendMailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:mail {to} {subject} {text}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a Mail';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->line('Sending eMail to: '.$this->argument('to'));

        $recipient = $this->argument('to');
        $subject = $this->argument('subject');
        $body = $this->argument('text');

        Mail::send([], [], function (Message $message) use ($recipient, $subject, $body) {
            $message->to($recipient)
                ->subject($subject)
                ->html($body);
        });
    }
}
