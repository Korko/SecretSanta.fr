<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\Attributes\Backoff;
use Illuminate\Queue\Attributes\Tries;
use Illuminate\Support\Facades\Lang;

// Same target, new name: not tracked, the santa's target email keeps its own delivery status
#[Backoff(30)]
#[Tries(10)]
final class TargetRenamed extends Notification implements ShouldBeEncrypted, ShouldQueueAfterCommit
{
    use Queueable;

    public function __construct(private readonly string $oldName, private readonly string $newName) {}

    /** @return list<string> */
    public function via(Participant $santa): array
    {
        return ['mail'];
    }

    public function toMail(Participant $santa): MailMessage
    {
        return (new MailMessage)
            ->subject((string) Lang::get('emails.target_renamed.title', ['draw' => $santa->draw->id]))
            ->view(['emails.target_renamed', 'emails.target_renamed_plain'], [
                'santaName' => $santa->name,
                'oldName' => $this->oldName,
                'newName' => $this->newName,
            ]);
    }
}
