<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Participant;
use App\Services\ParticipantsEditor;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Attributes\DeleteWhenMissingModels;
use Illuminate\Validation\ValidationException;

// A participant's withdrawal once its delay is over (withdraws_at), queued with that delay when they ask. Only a
// queued job can decrypt the draw later: like any queued email, it carries the draw secret until it runs
#[DeleteWhenMissingModels] // the organizer removed them in the meantime: nothing left to do
final class ApplyWithdrawal implements ShouldBeEncrypted, ShouldQueue
{
    use Queueable;

    public function __construct(public Participant $participant) {}

    public function handle(): void
    {
        $participant = $this->participant;

        // Cancelled (the organizer fixed their address), asked again since (that job will do it), or too late
        if ($participant->withdraws_at === null || $participant->withdraws_at->isFuture() || $participant->draw->expired) {
            return;
        }

        try {
            new ParticipantsEditor($participant->draw)->apply([$participant], []);
        } catch (ValidationException $validationException) {
            // The draw changed since they asked (an exclusion, too few participants left): their panel row still says
            // they leave, the organizer can remove them there, forcing it if needed
            report($validationException);

            return;
        }

        // The deleted model still has its ids
        $participant->createMetric('withdraw')->addTag('by', 'participant');
    }
}
