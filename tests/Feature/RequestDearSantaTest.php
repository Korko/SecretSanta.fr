<?php

use App\Models\Draw;
use App\Notifications\DearSanta;
use App\Notifications\TargetDrawn;
use Illuminate\Support\Facades\URL;

it('send to each participant a link to write to their santa', function () {
    Notification::fake();

    $draw = Draw::factory()
        ->hasParticipants(3)
        ->create();

    foreach($draw->participants as $participant) {
        Notification::assertSentTo($participant, function (TargetDrawn $notification) use ($participant) {
            $link = $notification->toMail($participant)->data()['dearSantaLink'];

            // Check the dearsanta link is valid
            test()->get($link)->assertSuccessful();

            // Check link can be used for support
            $path = parse_url($link, PHP_URL_PATH);
            $guessedParticipant = URLParser::parseByName('dearsanta', $path)->participant;
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
        ajaxPost(URL::signedRoute('dearsanta.contact', ['participant' => $participant->hash]), [
                'content' => 'test dearsanta mail content',
            ])
            ->assertSuccessful()
            ->assertJsonStructure(['message']);

        Notification::assertSentTo($participant->santa, DearSanta::class);
    }
});