<?php

namespace App\Console\Commands;

use App\Notifications\DearSanta as DearSantaNotification;
use App\Models\DearSanta;
use DrawCrypt;
use Illuminate\Console\Command;

class SendDearSanta extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:dearSanta {dearSanta : ID of the dearsanta} {iv : The decryption iv}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send again an email to a santa';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $iv = base64_decode($this->argument('iv'));
        DrawCrypt::setIV($iv);

        $dearSanta = DearSanta::find($this->argument('dearSanta'));

        $dearSanta->sender->santa->notify(new DearSantaNotification($dearSanta));
        $this->info('DearSanta mail sent');
    }
}