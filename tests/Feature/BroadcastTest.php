<?php

declare(strict_types=1);

use App\Events\MailStatusUpdated;
use App\Models\Draw;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

test('mail statuses are broadcast on channels the URL hashes do not give away', function (): void {
    Notification::fake();
    $draw = Draw::factory()->hasParticipants(3)->create();
    $participant = $draw->participants->first();

    // Target emails: the organizer panel
    $channel = (new MailStatusUpdated($participant->mail))->broadcastOn()[0]->name;
    expect($channel)->toBe($draw->broadcastChannel())->not->toContain($draw->hash);
    ajaxGet(URL::signedRoute('organizerPanel.fetch', ['draw' => $draw]))->assertJsonPath('channel', $channel);

    // Dear Santa messages: only their sender
    ajaxPost(URL::signedRoute('dearSanta.contact', ['participant' => $participant]), ['content' => 'Hello'])->assertSuccessful();
    $dearSanta = $participant->dearSantas()->firstOrFail();
    expect((new MailStatusUpdated($dearSanta->mail))->broadcastOn()[0]->name)
        ->toBe($participant->broadcastChannel())
        ->not->toBe($participant->santa->broadcastChannel());

    ajaxGet(URL::signedRoute('dearSanta.fetch', ['participant' => $participant]))
        ->assertJsonPath('channel', $participant->broadcastChannel())
        ->assertJsonMissingPath('draw');
});
