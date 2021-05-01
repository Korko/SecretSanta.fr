<?php

use App\Jobs\ParseBounces;
use App\Models\Draw;
use App\Models\Mail as MailModel;
use Facades\App\Services\MailTracker;

it('can handle bounced emails', function () {
    ajaxPost('/', [
            'participants'    => generateParticipants(3),
            'title'           => 'this is a test',
            'content-email'   => 'test mail {SANTA} => {TARGET}',
            'data-expiration' => date('Y-m-d', strtotime('+2 days')),
        ])
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    $draw = Draw::find(1);

    [$bouncedParticipant, $confirmedParticipant] = $draw->participants->random(2);

    $job = $this->partialMock(ParseBounces::class);
    $job->shouldAllowMockingProtectedMethods()
        ->shouldReceive('getRecipients')
        ->andReturn([
            MailTracker::getBounceReturnPath($bouncedParticipant->mail),
            MailTracker::getConfirmReturnPath($confirmedParticipant->mail),
        ]);

    app()->call([$job, 'handle']);

    test()->assertEquals(MailModel::ERROR, $bouncedParticipant->fresh()->mail->delivery_status);
    test()->assertEquals(MailModel::RECEIVED, $confirmedParticipant->fresh()->mail->delivery_status);

    //TODO Add pixel test?
});