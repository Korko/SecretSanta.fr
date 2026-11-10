<?php

declare(strict_types=1);

use App\Models\Draw;
use App\Notifications\DearSanta;
use App\Notifications\TargetDrawn;
use App\Services\URLParser;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

use function PHPUnit\Framework\assertEquals;

it('send to each participant a link to write to their santa', function (): void {
    Notification::fake();

    $draw = Draw::factory()
        ->hasParticipants(3)
        ->create();

    foreach ($draw->participants as $participant) {
        Notification::assertSentTo($participant, function (TargetDrawn $notification) use ($participant): true {
            $link = $notification->toMail($participant)->data()['dearSantaLink'];

            // Check the dearSanta link is valid
            test()->get($link)->assertSuccessful();

            // Check link can be used for support
            $path = parse_url($link, PHP_URL_PATH);
            $guessedParticipant = resolve(URLParser::class)->participant((string) $path);
            assertEquals($participant->id, $guessedParticipant->id);

            return true;
        });
    }
});

it('lets each participant write to their santa', function (): void {
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

it('lets a participant resend the email to their santa in case of error', function (): void {
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

    ajaxPost(URL::signedRoute('dearSanta.resend', ['participant' => $participant, 'dearSanta' => $dearSanta]))
        ->assertForbidden()
        ->assertJsonStructure(['message']);

    // Nothing new, still the same notification
    Notification::assertSentToTimes($participant->santa, DearSanta::class, 1);

    $dearSanta->mail->updated_at = $dearSanta->mail->updated_at->subSeconds(config('mail.resend_delay'));
    $dearSanta->mail->save();

    ajaxPost(URL::signedRoute('dearSanta.resend', ['participant' => $participant, 'dearSanta' => $dearSanta]))
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    Notification::assertSentToTimes($participant->santa, DearSanta::class, 2);
});

it('does not resend anything on a GET, like a link prefetch or an email scanner', function (): void {
    $draw = Draw::factory()->hasParticipants(3)->create();
    $participant = $draw->participants->first();

    ajaxGet(URL::signedRoute('dearSanta.resend_target', ['participant' => $participant]))
        ->assertStatus(405);
});
