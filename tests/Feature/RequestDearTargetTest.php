<?php

use App\Enums\QuestionToSanta;
use App\Models\DearTarget;
use App\Models\Draw;
use App\Notifications\DearTarget as DearTargetNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

it('lets each santa write to their target', function (Draw $draw) {
    Notification::fake();

    foreach ($draw->santas as $participant) {
        ajaxPost(URL::signedRoute('santa.contactTarget', ['participant' => $participant]), [
                'type' => QuestionToSanta::cases()[0]->value,
            ])
            ->assertSuccessful()
            ->assertJsonStructure(['message']);

        Notification::assertSentTo($participant->target, DearTargetNotification::class);
    }
})->with('basic draw');

test('it does not let a participant resend the email to their target just after sending', function (DearTarget $dearTarget) {
    Notification::fake();

    ajaxGet(URL::signedRoute('santa.resendDearTarget', ['participant' => $dearTarget->sender, 'dearTarget' => $dearTarget]))
        ->assertForbidden()
        ->assertJsonStructure(['message']);

    // Nothing new, still the same notification
    Notification::assertNothingSent();
})->with('dear target');

test('it lets a participant resend the email to their target in case of error', function (DearTarget $dearTarget) {
    Notification::fake();

    ajaxGet(URL::signedRoute('santa.resendDearTarget', ['participant' => $dearTarget->sender, 'dearTarget' => $dearTarget]))
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    Notification::assertSentToTimes($dearTarget->sender->target, DearTargetNotification::class, 1);
})->with('resendable dear target');

test('it updates the draw update date when writing to a target', function (Draw $draw) {
    Notification::fake();

    $updated_at = $draw->updated_at;
    $participant = $draw->santas->first();

    sleep(2);

    ajaxPost(URL::signedRoute('santa.contactTarget', ['participant' => $participant]), [
            'type' => QuestionToSanta::cases()[0]->value,
        ])
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    Notification::assertSentTo($participant->target, DearTargetNotification::class);
    $this->assertNotEquals($updated_at->timestamp, $draw->fresh()->updated_at->timestamp);
})->with('basic draw');
