<?php

namespace App\Console\Commands;

use Arr;
use DrawCrypt;
use Illuminate\Console\Command;
use URL;
use URLParser;
use App\Models\Draw;
use App\Models\Mail;

class ListDraws extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'secretsanta:list-draws';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List draws';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $draws = Draw::where('expires_at', '>', now())->get();
        foreach($draws as $draw) {
                $this->info('Draw #'.$draw->id.' ('.$draw->created_at.' - '.$draw->expires_at.')');
                $this->table(
                        ['Participants', 'Created', 'Sending', 'Sent', 'Received', 'Bounced'],
                        [[
                                $draw->participants->count(),
                                $draw->participants->filter(fn($p) => $p->mail->delivery_status === Mail::CREATED)->count(),
                                $draw->participants->filter(fn($p) => $p->mail->delivery_status === Mail::SENDING)->count(),
                                $draw->participants->filter(fn($p) => $p->mail->delivery_status === Mail::SENT)->count(),
                                $draw->participants->filter(fn($p) => $p->mail->delivery_status === Mail::RECEIVED)->count(),
                                $draw->participants->filter(fn($p) => $p->mail->delivery_status === Mail::ERROR)->count(),
                        ]]
                );
                $this->newLine();
        }
    }
}
