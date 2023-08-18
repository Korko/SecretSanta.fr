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

    expect($bouncedParticipant->fresh()->mail->delivery_status)->toBe(MailModel::STATE_ERROR);
    expect($confirmedParticipant->fresh()->mail->delivery_status)->toBe(MailModel::STATE_RECEIVED);
})->with('basic draw');
