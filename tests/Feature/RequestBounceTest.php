<?php

use App\Jobs\ParseBounces;
use App\Models\Draw;
use App\Models\Mail as MailModel;
use Facades\App\Services\MailTracker;
use function Pest\Laravel\partialMock;

it('can handle bounced emails', function (Draw $draw) {
    [$bouncedParticipant, $confirmedParticipant] = $draw->participants->random(2);

    $job = partialMock(ParseBounces::class);
    $job->shouldAllowMockingProtectedMethods()
        ->shouldReceive('getRecipients')
        ->andReturn([
            MailTracker::getBounceReturnPath($bouncedParticipant->mail),
            MailTracker::getConfirmReturnPath($confirmedParticipant->mail),
        ]);

    app()->call([$job, 'handle']);

    assertEquals(MailModel::STATE_ERROR, $bouncedParticipant->fresh()->mail->delivery_status);
    assertEquals(MailModel::STATE_RECEIVED, $confirmedParticipant->fresh()->mail->delivery_status);
})->with('basic draw');
