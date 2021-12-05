<?php

namespace App\Console\Commands;

use App\Notifications\DearSanta as DearSantaNotification;
use App\Models\DearSanta;

class SendDearSanta extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:dearSanta {dearSanta : ID of the dearsanta} {url : The URL received by one of the participants to write to their santa}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send again an email to a santa';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->setCryptIVFromUrl($this->argument('url'));

        $dearSanta = DearSanta::find($this->argument('dearSanta'));

        $dearSanta->sender->santa->notifyNow(new DearSantaNotification($dearSanta));
        $this->info('DearSanta mail sent');
    }
}
