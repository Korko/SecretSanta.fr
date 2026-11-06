<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;

#[Description('Send a Mail')]
#[Signature('send:mail {to} {subject} {text}')]
final class SendMailCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->line('Sending eMail to: '.$this->argument('to'));

        $recipient = $this->argument('to');
        $subject = $this->argument('subject');
        $body = $this->argument('text');

        Mail::send([], [], function (Message $message) use ($recipient, $subject, $body): void {
            $message->to($recipient)
                ->subject($subject)
                ->setBody($body);
        });
    }
}
