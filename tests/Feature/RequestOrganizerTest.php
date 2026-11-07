<?php

declare(strict_types=1);

use App\Models\Draw;
use App\Notifications\DearSanta;
use App\Notifications\OrganizerRecap;
use App\Notifications\TargetChanged;
use App\Notifications\TargetDrawn;
use App\Notifications\TargetWithdrawn;
use Illuminate\Support\Facades\App;
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

    ajaxPost(URL::signedRoute('organizerPanel.changeParticipants', ['draw' => $draw]), ['remove' => [$participant->hash]])
        ->assertSuccessful()
        ->assertJsonStructure(['participants']);

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

    ajaxPost(URL::signedRoute('organizerPanel.changeParticipants', ['draw' => $draw]), ['remove' => [$b->hash]])
        ->assertSuccessful();

    $otherSanta = $a->fresh()->santa;
    assertTrue(in_array($otherSanta->id, [$c->id, $d->id], true));
    assertTrue(in_array($a->fresh()->target->id, [$c->id, $d->id], true));
    Notification::assertSentTo($a, TargetWithdrawn::class);
    Notification::assertSentTo($otherSanta, TargetChanged::class);
});

test('the organizer can add a participant, only their santa is notified', function (): void {
    $draw = Draw::factory()->hasParticipants(4)->create();
    Notification::fake();
    $before = $draw->participants->mapWithKeys(fn ($p): array => [$p->id => $p->target->id]);

    ajaxPost(URL::signedRoute('organizerPanel.changeParticipants', ['draw' => $draw]), [
        'add' => [['name' => 'Newbie', 'email' => 'newbie@example.com']],
    ])->assertSuccessful()->assertJsonCount(5, 'participants');

    $new = $draw->fresh()->participants->firstWhere('name', 'Newbie');
    $santa = $new->santa;
    assertEquals($before[$santa->id], $new->target->id);
    Notification::assertSentTo($new, TargetDrawn::class);
    Notification::assertSentTo($santa, TargetChanged::class);
    Notification::assertSentTimes(TargetChanged::class, 1);
    Notification::assertSentTimes(TargetDrawn::class, 1);
});

test('the organizer can add and remove several participants, each santa gets one email', function (): void {
    $draw = Draw::factory()->hasParticipants(6)->create();
    Notification::fake();
    [$a, $b] = $draw->participants->all();

    ajaxPost(URL::signedRoute('organizerPanel.changeParticipants', ['draw' => $draw]), [
        'remove' => [$a->hash, $b->hash],
        'add' => [['name' => 'N1', 'email' => 'n1@example.com'], ['name' => 'N2', 'email' => 'n2@example.com'], ['name' => 'N3', 'email' => 'n3@example.com']],
    ])->assertSuccessful()->assertJsonCount(7, 'participants');

    $draw = $draw->fresh();
    assertEquals(7, $draw->participants->count());
    assertEquals(7, $draw->participants->pluck('target_id')->filter()->unique()->count());
    Notification::assertSentTimes(TargetDrawn::class, 3);
    foreach ($draw->participants as $participant) {
        // At most one email per santa, whatever the number of changes
        $emails = Notification::sent($participant, TargetChanged::class)->count() + Notification::sent($participant, TargetWithdrawn::class)->count();
        assertTrue($emails <= 1, "{$participant->name} got {$emails} emails");
    }
});

test('the organizer cannot add a name already taken nor go under 3 participants', function (): void {
    $draw = Draw::factory()->hasParticipants(3)->create();
    $url = URL::signedRoute('organizerPanel.changeParticipants', ['draw' => $draw]);

    ajaxPost($url, ['add' => [['name' => $draw->participants->first()->name, 'email' => 'x@example.com']]])->assertStatus(422);
    ajaxPost($url, ['remove' => [$draw->participants->first()->hash]])->assertStatus(422);
    assertEquals(3, $draw->fresh()->participants->count());
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
    $participant = $draw->participants->first();

    $csv = ajaxGet(URL::signedRoute('organizerPanel.csvFinal', ['draw' => $draw]))
        ->assertHeader('Content-Type', 'text/csv; charset=UTF-8')
        ->assertSuccessful()
        ->getContent();

    expect($csv)->toStartWith("\xEF\xBB\xBF")
        ->and(array_map(str_getcsv(...), explode("\n", mb_substr((string) $csv, 1))))
        ->toContain([$participant->name, $participant->email, $participant->target->name]);
});

test('the organizer recap dates follow the email locale', function (): void {
    $draw = Draw::factory()->create(['expires_at' => '2026-12-24']);

    App::setLocale('en');
    expect(OrganizerRecap::viewData($draw)['expirationDate'])->toBe('December 24, 2026');

    App::setLocale('fr');
    expect(OrganizerRecap::viewData($draw)['expirationDate'])->toBe('24 décembre 2026');
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

test('the organizer can change the event date', function (): void {
    $draw = Draw::factory()->hasParticipants(3)->create();
    $path = URL::signedRoute('organizerPanel.changeDate', ['draw' => $draw]);
    $date = now()->addDays(20)->format('Y-m-d');

    ajaxPost($path, ['expires_at' => $date])->assertSuccessful();
    assertEquals($date, $draw->refresh()->expires_at->format('Y-m-d'));

    ajaxPost($path, ['expires_at' => now()->subDay()->format('Y-m-d')])->assertStatus(422);
    assertEquals($date, $draw->refresh()->expires_at->format('Y-m-d'));
});

test('the organizer cannot change the date of an expired draw', function (): void {
    $draw = Draw::factory()->hasParticipants(3)->expired()->create();

    ajaxPost(URL::signedRoute('organizerPanel.changeDate', ['draw' => $draw]), ['expires_at' => now()->addDays(5)->format('Y-m-d')])
        ->assertForbidden();
});

test('a participant report shows on the organizer panel, and fixing the address clears it', function (): void {
    $draw = Draw::factory()->hasParticipants(3)->create();
    $participant = $draw->participants->first();

    ajaxPost(URL::signedRoute('dearSanta.report', ['participant' => $participant]), ['reason' => 'not_me'])
        ->assertSuccessful()->assertJson(['report' => 'not_me']);
    ajaxPost(URL::signedRoute('dearSanta.report', ['participant' => $participant]), ['reason' => 'whatever'])
        ->assertStatus(422);

    ajaxGet(URL::signedRoute('organizerPanel.fetch', ['draw' => $draw]))
        ->assertJsonPath("participants.{$participant->hash}.report", 'not_me');

    // The reported address stays refused in this draw: resent, given to someone else, or re-added after removal
    $reported = $participant->email;
    ajaxPost(URL::signedRoute('organizerPanel.changeEmail', ['draw' => $draw, 'participant' => $participant]), ['email' => mb_strtoupper($reported)])
        ->assertStatus(422)->assertJsonValidationErrors('email');
    ajaxPost(URL::signedRoute('organizerPanel.changeEmail', ['draw' => $draw, 'participant' => $draw->participants->last()]), ['email' => $reported])
        ->assertStatus(422);
    ajaxPost(URL::signedRoute('organizerPanel.changeParticipants', ['draw' => $draw]), ['add' => [['name' => 'Back', 'email' => $reported]]])
        ->assertStatus(422)->assertJsonValidationErrors('add.0.email');

    ajaxPost(URL::signedRoute('organizerPanel.changeEmail', ['draw' => $draw, 'participant' => $participant]), ['email' => 'fixed@example.com'])
        ->assertSuccessful();
    assertNull($participant->fresh()->report);

    ajaxPost(URL::signedRoute('dearSanta.report', ['participant' => $participant]), ['reason' => 'not_participating']);
    ajaxPost(URL::signedRoute('dearSanta.report', ['participant' => $participant]), [])->assertJson(['report' => null]);
    assertNull($participant->fresh()->report);
});

test('the organizer taking part cannot report themselves', function (): void {
    $draw = Draw::factory()->hasParticipants(3)->create();
    $organizer = $draw->participants->first();
    $draw->update(['organizer_email' => $organizer->email]);

    ajaxPost(URL::signedRoute('dearSanta.report', ['participant' => $organizer]), ['reason' => 'not_me'])
        ->assertForbidden();
    assertNull($organizer->fresh()->report);
});
