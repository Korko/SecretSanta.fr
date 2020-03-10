<?php

namespace App\Console\Commands;

use App\Mail;
use Hashids;
use Illuminate\Console\Command;
use Webklex\IMAP\Facades\Client as EmailClient;

class ListEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $oClient = EmailClient::account('default');
        $oClient->connect();
        $oFolder = $oClient->getFolder('INBOX');

        $to = $oFolder->query()->unseen()->limit(1)->get()->first()->getTo()[0]->mailbox;
        $hash = sscanf($to, str_replace('*', '%s', config('mail.return_path')))[0];

        $id = Hashids::connection('bounce')->decode($hash)[0];
        $mail = Mail::find($id);
        $mail->delivery_status = Mail::ERROR;
        $mail->save();
    }
}
