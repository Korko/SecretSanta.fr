<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\DeliveryStatus;
use App\Models\Draw;
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
                    $draw->participants->filter(fn ($p): bool => $p->mail->delivery_status === DeliveryStatus::Created)->count(),
                    $draw->participants->filter(fn ($p): bool => $p->mail->delivery_status === DeliveryStatus::Sending)->count(),
                    $draw->participants->filter(fn ($p): bool => $p->mail->delivery_status === DeliveryStatus::Sent)->count(),
                    $draw->participants->filter(fn ($p): bool => $p->mail->delivery_status === DeliveryStatus::Received)->count(),
                    $draw->participants->filter(fn ($p): bool => $p->mail->delivery_status === DeliveryStatus::Error)->count(),
                ]]
            );
            $this->newLine();
        }
    }
}
