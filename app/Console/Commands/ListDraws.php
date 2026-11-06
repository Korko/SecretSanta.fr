<?php

namespace App\Console\Commands;

use App\Models\Draw;
use App\Models\Mail;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Description('List draws')]
#[Signature('secretsanta:list-draws')]
final class ListDraws extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $draws = Draw::query()->where('expires_at', '>', now())->get();
        foreach ($draws as $draw) {
            $this->info('Draw #'.$draw->id.' ('.$draw->created_at.' - '.$draw->expires_at.')');
            $this->table(
                ['Participants', 'Created', 'Sending', 'Sent', 'Received', 'Bounced'],
                [[
                    $draw->participants->count(),
                    $draw->participants->filter(fn ($p): bool => $p->mail->delivery_status === Mail::CREATED)->count(),
                    $draw->participants->filter(fn ($p): bool => $p->mail->delivery_status === Mail::SENDING)->count(),
                    $draw->participants->filter(fn ($p): bool => $p->mail->delivery_status === Mail::SENT)->count(),
                    $draw->participants->filter(fn ($p): bool => $p->mail->delivery_status === Mail::RECEIVED)->count(),
                    $draw->participants->filter(fn ($p): bool => $p->mail->delivery_status === Mail::ERROR)->count(),
                ]]
            );
            $this->newLine();
        }
    }
}
