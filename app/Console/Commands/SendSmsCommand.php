<?php

namespace App\Console\Commands;

use Korko\Callr\CallrSmsCommand;

class SendSmsCommand extends CallrSmsCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:sms {phone} {text}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send an SMS';
}
