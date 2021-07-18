<?php

use App\Models\Draw;
use App\Models\Participant;
use App\Mail\SuggestRedraw as SuggestRedrawMail;
use App\Notifications\SuggestRedraw as SuggestRedrawNotification;
use App\Notifications\TargetDrawn;

test('the organizer can organize a redraw', function () {
    Notification::fake();

    $draw = Draw::factory()
        ->hasParticipants(3)
        ->create();

    assertFalse($draw->fresh()->redraw);

    $path = URL::signedRoute('organizerPanel.suggestRedraw', [
        'draw' => $draw
    ]);

    ajaxGet($path)
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    assertTrue($draw->fresh()->redraw);

    $draw->participants->each(function ($participant) {
        Notification::assertSentTo($participant, SuggestRedrawNotification::class);
    });
});

test('the organizer cannot organize a redraw if only one solution possible', function ($participants, $targets) {
    $draw = createServiceDraw($participants);

    assertFalse($draw->fresh()->redraw);

    $path = URL::signedRoute('organizerPanel.suggestRedraw', [
        'draw' => $draw
    ]);

    ajaxGet($path)
        ->assertForbidden()
        ->assertJsonStructure(['message']);

    assertFalse($draw->fresh()->redraw);
})->with('unique participants list');

test('participants can accept the redraw by mail', function () {
    Notification::fake();

    $draw = Draw::factory()
        ->hasParticipants(3)
        ->create();

    $path = URL::signedRoute('organizerPanel.suggestRedraw', [
        'draw' => $draw
    ]);
    ajaxGet($path)
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    foreach($draw->participants as $participant) {
        Notification::assertSentTo($participant, function (SuggestRedrawNotification $notification) use ($participant) {
            $link = $notification->toMail($participant)->data()['acceptLink'];

            $guessedParticipant = URLParser::parseByName('acceptRedraw', $link)->participant;
            assertEquals($participant->id, $guessedParticipant->id);

            assertFalse($participant->redraw);

            test()->get($link)->assertSuccessful();

            assertTrue($participant->fresh()->redraw);

            return true;
        });
    }
});

test('participants cannot accept the redraw if closed', function () {
    Notification::fake();

    $draw = Draw::factory()
        ->hasParticipants(3)
        ->create();

    $path = URL::signedRoute('organizerPanel.suggestRedraw', [
        'draw' => $draw
    ]);
    ajaxGet($path)
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    $draw->update(['redraw' => false]);

    foreach($draw->participants as $participant) {
        Notification::assertSentTo($participant, function (SuggestRedrawNotification $notification) use ($participant) {
            $link = $notification->toMail($participant)->data()['acceptLink'];

            test()->get($link)->assertStatus(403);

            $participant = URLParser::parseByName('acceptRedraw', $link)->participant;
            assertFalse($participant->fresh()->redraw);

            return true;
        });
    }
});

test('the organizer cannot process the redraw until a solution is possible', function () {
    $draw = Draw::factory()
        ->redrawing()
        ->hasParticipants(3)
        ->create();

    $this->assertFalse($draw->canRedraw);

    $draw->participants[0]->redraw = true;
    $draw->participants[0]->save();

    $this->assertFalse($draw->canRedraw);

    $draw->participants[1]->redraw = true;
    $draw->participants[1]->save();

    $this->assertFalse($draw->canRedraw);

    $draw->participants[2]->redraw = true;
    $draw->participants[2]->save();

    $this->assertTrue($draw->canRedraw);
});

test('the organizer can process the redraw', function () {
    Notification::fake();

    $draw = Draw::factory()
        ->redrawing()
        ->hasParticipants(3, ['redraw' => true])
        ->create();

    $target1 = $draw->participants[0]->target;
    $target2 = $draw->participants[1]->target;
    $target3 = $draw->participants[2]->target;

    Notification::assertTimesSent(count($draw->participants), TargetDrawn::class);

    $path = URL::signedRoute('organizerPanel.redraw', [
        'draw' => $draw
    ]);
    ajaxGet($path)
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    Notification::assertTimesSent(count($draw->participants) * 2, TargetDrawn::class);

    $draw->participants = $draw->participants->fresh();
    $this->assertNotEquals($target1->id, $draw->participants[0]->target->id);
    $this->assertNotEquals($target2->id, $draw->participants[1]->target->id);
    $this->assertNotEquals($target3->id, $draw->participants[2]->target->id);
});