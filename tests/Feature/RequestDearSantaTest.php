<?php

use App\Models\Draw;
use App\Models\Mail as MailModel;
use App\Notifications\DearSanta;
use App\Notifications\TargetDrawn;
use Illuminate\Support\Facades\URL;
/*
it('send to each participant a link to write to their santa', function () {
    Notification::fake();

    $draw = Draw::factory()
        ->hasParticipants(3)
        ->create();

    foreach($draw->participants as $participant) {
        Notification::assertSentTo($participant, function (TargetDrawn $notification) use ($participant) {
            $link = $notification->toMail($participant)->data()['dearSantaLink'];

            // Check the dearSanta link is valid
            test()->get($link)->assertSuccessful();

            // Check link can be used for support
            $path = parse_url($link, PHP_URL_PATH);
            $guessedParticipant = URLParser::parseByName('dearSanta', $path)->participant;
            assertEquals($participant->id, $guessedParticipant->id);

            return true;
        });
    }
});

it('lets each participant write to their santa', function () {
    Notification::fake();

    $draw = Draw::factory()
        ->hasParticipants(3)
        ->create();

    foreach ($draw->participants as $participant) {
        ajaxPost(URL::signedRoute('dearSanta.contact', ['participant' => $participant]), [
                'content' => 'test dearSanta mail content',
            ])
            ->assertSuccessful()
            ->assertJsonStructure(['message']);

        Notification::assertSentTo($participant->santa, DearSanta::class);
    }
});
*/
it('lets a participant resend the email to their santa in case of error', function () {
    Notification::fake();

    $draw = Draw::factory()
        ->hasParticipants(3)
        ->create();

    $participant = $draw->participants->first();

    ajaxPost(URL::signedRoute('dearSanta.contact', ['participant' => $participant]), [
            'content' => 'test dearSanta mail content',
        ])
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    Notification::assertSentToTimes($participant->santa, DearSanta::class, 1);
    $dearSanta = $participant->dearSantas->first();
/*
    ajaxGet(URL::signedRoute('dearSanta.resend', ['participant' => $participant, 'dearSanta' => $dearSanta]))
        ->assertForbidden()
        ->assertJsonStructure(['message']);

    // Nothing new, still the same notification
    Notification::assertSentToTimes($participant->santa, DearSanta::class, 1);
*/
    // Actual notification is not sent so no real mail notification is recorded
    // Fake one
    $mail = MailModel::factory()
        ->for($dearSanta, 'mailable')
        ->failed()
        ->create();

    ajaxGet(URL::signedRoute('dearSanta.resend', ['participant' => $participant, 'dearSanta' => $dearSanta]))
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

//    Notification::assertSentToTimes($participant->santa, DearSanta::class, 2);
});