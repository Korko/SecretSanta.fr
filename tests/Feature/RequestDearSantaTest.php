<?php

use App\Mail\TargetDrawn;
use App\Models\Draw;
use App\Notifications\DearSanta;
use Illuminate\Support\Facades\URL;

it('send to each participant a link to write to their santa', function () {
    Mail::fake();

    $draw = Draw::factory()
        ->hasParticipants(3)
        ->create();

    $links = [];
    Mail::assertSent(function (TargetDrawn $mail) use (&$links) {
        return $links[] = $mail->dearSantaLink;
    });
    assertEquals(count($draw->participants), count($links));

    foreach ($links as $id => $link) {
        // Check the dearsanta link is valid
        test()->get($link)->assertSuccessful();

        // Check link can be used for support
        $path = parse_url($link, PHP_URL_PATH);
        $participant = URLParser::parseByName('dearsanta', $path)->participant;
        assertEquals($draw->participants[$id]->santa->id, $participant->santa->id);
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