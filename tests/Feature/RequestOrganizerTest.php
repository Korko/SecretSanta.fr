<?php

declare(strict_types=1);

use App\Models\Draw;
use App\Notifications\DearSanta;
use App\Notifications\TargetChanged;
use App\Notifications\TargetDrawn;
use App\Notifications\TargetWithdrawn;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotEquals;
use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertTrue;

test('the organizer can send again the target drawn email', function (): void {
    Notification::fake();

    $draw = Draw::factory()
        ->hasParticipants(3)
        ->create();
    $participant = $draw->participants->random();

    $path = URL::signedRoute('organizerPanel.changeEmail', [
        'draw' => $draw,
        'participant' => $participant,
    ]);

    ajaxPost($path, ['email' => $participant->email])
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    Notification::assertSentTo($participant, TargetDrawn::class);
});

test("the organizer can change a participant's email", function (): void {
    Notification::fake();

    $draw = Draw::factory()
        ->hasParticipants(3)
        ->create();
    $participant = $draw->participants->random();

    $path = URL::signedRoute('organizerPanel.changeEmail', [
        'draw' => $draw,
        'participant' => $participant,
    ]);

    $before = $participant->email;

    ajaxPost($path, ['email' => 'test@test2.com'])
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    $participant = $participant->fresh();
    $after = $participant->email;

    assertNotEquals($before, $after);
    assertEquals('test@test2.com', $after);

    Notification::assertSentTo($participant, TargetDrawn::class);
});

test('the organizer can withdraw a participant', function (): void {
    Notification::fake();

    // With 5 participants, at least one is in a cycle of 3+ (a 2-cycle can't be withdrawn)
    $draw = Draw::factory()
        ->hasParticipants(5)
        ->create();
    $participant = $draw->participants->first(fn ($participant) => $participant->santa->isNot($participant->target));

    ajaxPost(URL::signedRoute('dearSanta.contact', ['participant' => $participant]), [
        'content' => 'test dearSanta mail content',
    ])
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    Notification::assertSentTo($participant->santa, DearSanta::class);

    $santa = $participant->santa;
    $target = $participant->target;

    ajaxGet(URL::signedRoute('organizerPanel.withdraw', [
        'draw' => $draw,
        'participant' => $participant,
    ]))
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    $santa = $santa->fresh();
    $participant = $participant->fresh();
    $target = $target->fresh();

    assertEquals($santa->target->id, $target->id);
    assertEquals($target->santa->id, $santa->id);

    Notification::assertSentTo($santa, TargetWithdrawn::class);
    Notification::assertSentTo($santa, DearSanta::class);
});

test('the organizer can withdraw a participant in a 2-cycle', function (): void {
    Notification::fake();

    $draw = Draw::factory()->hasParticipants(4)->create();
    [$a, $b, $c, $d] = $draw->participants->all();
    // A <-> B, C <-> D
    foreach ([[$a, $b], [$b, $a], [$c, $d], [$d, $c]] as [$santa, $target]) {
        $target->santa()->associate($santa)->save();
    }

    ajaxGet(URL::signedRoute('organizerPanel.withdraw', ['draw' => $draw->fresh(), 'participant' => $b]))
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    $otherSanta = $a->fresh()->santa;
    assertTrue(in_array($otherSanta->id, [$c->id, $d->id], true));
    assertTrue(in_array($a->fresh()->target->id, [$c->id, $d->id], true));
    Notification::assertSentTo($a, TargetWithdrawn::class);
    Notification::assertSentTo($otherSanta, TargetChanged::class);
});

test('the organizer can download initial data', function (): void {
    $draw = Draw::factory()
        ->hasParticipants(3)
        ->create();

    $path = URL::signedRoute('organizerPanel.csvInit', [
        'draw' => $draw,
    ]);

    ajaxGet($path)
        ->assertHeader('Content-Type', 'text/csv; charset=UTF-8')
        ->assertSuccessful();
});

test('the organizer cannot download total data if the draw is not expired', function (): void {
    $draw = Draw::factory()
        ->hasParticipants(3)
        ->create();

    $path = URL::signedRoute('organizerPanel.csvFinal', [
        'draw' => $draw,
    ]);

    ajaxGet($path)->assertStatus(403);
});

test('the organizer can download total data if the draw is expired', function (): void {
    $draw = Draw::factory()
        ->hasParticipants(3)
        ->expired()
        ->create();

    $path = URL::signedRoute('organizerPanel.csvFinal', [
        'draw' => $draw,
    ]);

    ajaxGet($path)
        ->assertHeader('Content-Type', 'text/csv; charset=UTF-8')
        ->assertSuccessful();
});

test('the organizer can download total data once targets are revealed', function (): void {
    $draw = Draw::factory()
        ->hasParticipants(3)
        ->create(['targets_revealed_at' => now()]);

    ajaxGet(URL::signedRoute('organizerPanel.csvFinal', ['draw' => $draw]))
        ->assertHeader('Content-Type', 'text/csv; charset=UTF-8')
        ->assertSuccessful();
});

test('the organizer can delete all data before expiration', function (): void {
    $draw = Draw::factory()
        ->hasParticipants(3)
        ->create();

    $path = URL::signedRoute('organizerPanel.delete', [
        'draw' => $draw,
    ]);

    ajaxDelete($path)
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    assertNull($draw->fresh());
});

test('the organizer can delete all data after expiration', function (): void {
    $draw = Draw::factory()
        ->hasParticipants(3)
        ->expired()
        ->create();

    $path = URL::signedRoute('organizerPanel.delete', [
        'draw' => $draw,
    ]);

    ajaxDelete($path)
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    assertNull($draw->fresh());
});

test('the organizer can reveal the targets, once, and participants see it', function (): void {
    $draw = Draw::factory()->hasParticipants(3)->create();
    $participant = $draw->participants->first();

    $fetch = fn () => ajaxGet(URL::signedRoute('organizerPanel.fetch', ['draw' => $draw]))->assertSuccessful();
    $participantFetch = fn () => ajaxGet(URL::signedRoute('dearSanta.fetch', ['participant' => $participant]))->assertSuccessful();

    expect($fetch()->json('targets'))->toBeNull()
        ->and($participantFetch()->json('targetsRevealed'))->toBeFalse();

    $response = ajaxPost(URL::signedRoute('organizerPanel.revealTargets', ['draw' => $draw]))->assertSuccessful();
    $revealedAt = $draw->fresh()->targets_revealed_at;

    expect($revealedAt)->not->toBeNull()
        ->and($response->json('targets'))->toBe(
            $draw->participants->mapWithKeys(fn ($p): array => [$p->hash => $p->target->name])->all()
        );

    // Revealing again keeps the first date
    ajaxPost(URL::signedRoute('organizerPanel.revealTargets', ['draw' => $draw]))->assertSuccessful();
    expect($draw->fresh()->targets_revealed_at->equalTo($revealedAt))->toBeTrue()
        ->and($fetch()->json('targets'))->toHaveCount(3)
        ->and($participantFetch()->json('targetsRevealed'))->toBeTrue();
});
