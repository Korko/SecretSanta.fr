<?php

use App\Models\DearSanta;
use App\Models\Draw;
use App\Notifications\DearSanta as DearSantaNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

it('lets each participant write to their santa', function (Draw $draw) {
    Notification::fake();

    foreach ($draw->participants as $participant) {
        ajaxPost(URL::signedRoute('santa.contactSanta', ['participant' => $participant]), [
            'content' => 'test dearSanta mail content',
        ])
            ->assertSuccessful()
            ->assertJsonStructure(['message']);

        Notification::assertSentTo($participant->santa, DearSantaNotification::class);
    }
})->with('basic draw');

test('it does not let a participant resend the email to their santa just after sending', function (DearSanta $dearSanta) {
    Notification::fake();

    ajaxGet(URL::signedRoute('santa.resendDearSanta', ['participant' => $dearSanta->sender, 'dearSanta' => $dearSanta]))
        ->assertForbidden()
        ->assertJsonStructure(['message']);

    // Nothing new, still the same notification
    Notification::assertNothingSent();
})->with('dear santa');

test('it lets a participant resend the email to their santa in case of error', function (DearSanta $dearSanta) {
    Notification::fake();

    ajaxGet(URL::signedRoute('santa.resendDearSanta', ['participant' => $dearSanta->sender, 'dearSanta' => $dearSanta]))
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    Notification::assertSentToTimes($dearSanta->sender->santa, DearSantaNotification::class, 1);
})->with('resendable dear santa');

test('it updates the draw update date when writing to a santa', function (Draw $draw) {
    Notification::fake();

    $updated_at = $draw->updated_at;
    $participant = $draw->participants->first();

    sleep(2);

    ajaxPost(URL::signedRoute('santa.contactSanta', ['participant' => $participant]), [
        'content' => 'test dearSanta mail content',
    ])
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    Notification::assertSentTo($participant->santa, DearSantaNotification::class);
    $this->assertNotEquals($updated_at->timestamp, $draw->fresh()->updated_at->timestamp);
})->with('basic draw');
