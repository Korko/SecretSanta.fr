<?php

declare(strict_types=1);

use App\Enums\DeliveryStatus;
use App\Models\Draw;
use App\Notifications\OrganizerEmailChanged;
use App\Notifications\OrganizerRecap;
use App\Notifications\TargetDrawn;
use App\Notifications\TargetMessages;
use App\Services\DrawFormHandler;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Testing\TestResponse;

// The organizer doesn't take part: the recap is their only email
function createOrganizedDraw(string $organizerEmail): Draw
{
    return (new DrawFormHandler)
        ->withOrganizer(['name' => 'Orga', 'email' => $organizerEmail])
        ->withParticipants(generateParticipants(3))
        ->withExpiration(date('Y-m-d', strtotime('+2 days')))
        ->withTitle('test mail {SANTA} => {TARGET} title')
        ->withBody('test mail {SANTA} => {TARGET} body')
        ->save();
}

function fixOrganizer(Draw $draw, string $email): TestResponse
{
    $url = URL::signedRoute('dearSanta', ['participant' => $draw->participants->last()->hash]).'#'.base64_encode(DrawCrypt::getIV());

    return ajaxPost(URL::route('fixOrganizer.handle'), ['url' => $url, 'email' => $email]);
}

test('the organizer can fix a mistyped address until they received their email', function (): void {
    Notification::fake();
    $draw = createServiceDraw(generateParticipants(3));
    $draw->update(['organizer_name' => 'Orga', 'organizer_email' => 'orga@exampel.com']);
    $this->travel(10)->minutes();

    fixOrganizer($draw, 'orga@example.com')->assertSuccessful();
    // The previous address is told, in case it wasn't them
    Notification::assertSentOnDemand(OrganizerEmailChanged::class, fn ($n, $c, $notifiable): bool => array_key_exists('orga@exampel.com', $notifiable->routes['mail']));
    expect($draw->fresh()->organizer_email)->toBe('orga@example.com');

    // Opening the panel proves the address works: /fix can't redirect it anymore
    $this->travel(10)->minutes();
    ajaxGet(URL::signedRoute('organizerPanel.fetch', ['draw' => $draw]))->assertSuccessful();
    fixOrganizer($draw, 'orga@exampl.com')->assertStatus(422)->assertJsonValidationErrors('email');
    expect($draw->fresh()->organizer_email)->toBe('orga@example.com');
});

test('a participating organizer whose email was received cannot be fixed', function (): void {
    Notification::fake();
    $draw = createServiceDraw(generateParticipants(3));
    $organizer = $draw->participants->first();
    $draw->update(['organizer_name' => $organizer->name, 'organizer_email' => $organizer->email]);
    $organizer->mail->updateDeliveryStatus(DeliveryStatus::Received);
    $this->travel(10)->minutes();

    fixOrganizer($draw->fresh(), $organizer->email.'x')->assertStatus(422)->assertJsonValidationErrors('email');
});

test('the fix is locked for the first minutes after the draw, without telling when it opens', function (): void {
    Notification::fake();
    $draw = createServiceDraw(generateParticipants(3));
    $draw->update(['organizer_name' => 'Orga', 'organizer_email' => 'orga@exampel.com']);

    $tooEarly = trans('error.fixOrganizer.tooEarly', ['minutes' => 10]);

    fixOrganizer($draw, 'orga@example.com')->assertStatus(422)->assertJsonPath('errors.email.0', $tooEarly);
    $this->travel(9)->minutes();
    fixOrganizer($draw, 'orga@example.com')->assertStatus(422)->assertJsonPath('errors.email.0', $tooEarly);
    expect($draw->fresh()->organizer_email)->toBe('orga@exampel.com');

    $this->travel(1)->minutes();
    fixOrganizer($draw, 'orga@example.com')->assertSuccessful();
    expect($draw->fresh()->organizer_email)->toBe('orga@example.com');
});

test('an organizer whose recap was confirmed received cannot be fixed either', function (): void {
    Notification::fake();
    $draw = createServiceDraw(generateParticipants(3));
    $draw->update(['organizer_name' => 'Orga', 'organizer_email' => 'orga@exampel.com']);
    // A read receipt or an auto-reply on the recap's tracked return path
    $draw->recapMail()->markAsReceived();
    $this->travel(10)->minutes();

    fixOrganizer($draw->fresh(), 'orga@example.com')->assertStatus(422)->assertJsonValidationErrors('email');
    expect($draw->fresh()->organizer_email)->toBe('orga@exampel.com');
});

test('an organizer who received their email can have it sent again to the same address', function (): void {
    Notification::fake();
    $draw = createOrganizedDraw('orga@example.com');
    $this->travel(10)->minutes();
    ajaxGet(URL::signedRoute('organizerPanel.fetch', ['draw' => $draw]))->assertSuccessful();
    $this->travel(10)->minutes();

    fixOrganizer($draw, 'orga@example.com')->assertSuccessful()->assertJsonPath('message', trans('message.resentOrganizer'));
    Notification::assertSentToTimes($draw, OrganizerRecap::class, 1);
    // Nothing changed: no warning to the address
    Notification::assertSentOnDemandTimes(OrganizerEmailChanged::class, 0);
    expect($draw->fresh()->organizer_email)->toBe('orga@example.com');

    // Still throttled: a participant can't flood the organizer with it
    fixOrganizer($draw, 'orga@example.com')->assertStatus(422)->assertJsonPath('errors.email.0', trans('error.fixOrganizer.resendDelayTooShort'));
    Notification::assertSentToTimes($draw, OrganizerRecap::class, 1);
});

test('a participating organizer who received their email gets their target email again', function (): void {
    Notification::fake();
    $draw = createServiceDraw(generateParticipants(3));
    $organizer = $draw->participants->first();
    $draw->update(['organizer_name' => $organizer->name, 'organizer_email' => $organizer->email]);
    $organizer->mail->updateDeliveryStatus(DeliveryStatus::Received);
    $this->travel(10)->minutes();

    fixOrganizer($draw->fresh(), $organizer->email)->assertSuccessful()->assertJsonPath('message', trans('message.resentOrganizer'));
    // Once at the draw, once now. With the recap inside, like at the draw: no separate one
    Notification::assertSentToTimes($organizer, TargetDrawn::class, 2);
    Notification::assertSentTo($organizer, TargetDrawn::class, fn (TargetDrawn $notification): bool => $notification->toMail($organizer)->data()['organizer'] !== null);
    Notification::assertSentToTimes($draw, OrganizerRecap::class, 0);
    Notification::assertSentOnDemandTimes(OrganizerEmailChanged::class, 0);
    expect($organizer->fresh()->email)->toBe($organizer->email);
});

test('a participating organizer fixing their address gets their target email there, with what their target wrote', function (): void {
    Notification::fake();
    $draw = createServiceDraw(generateParticipants(3));
    $organizer = $draw->organizer;
    $organizer->target->dearSantas()->create(['mail_body' => 'Un pull ?']);
    $mistyped = $organizer->email;
    $this->travel(10)->minutes();

    fixOrganizer($draw->fresh(), $mistyped.'x')->assertSuccessful()->assertJsonPath('message', trans('message.fixedOrganizer'));

    expect($organizer->fresh()->email)->toBe($mistyped.'x')
        ->and($draw->fresh()->organizer_email)->toBe($mistyped.'x');
    Notification::assertSentToTimes($draw, OrganizerRecap::class, 0);
    // Like any other change of address: the new one never got the messages of their target, they come in that email
    Notification::assertSentToTimes($organizer, TargetDrawn::class, 2);
    Notification::assertSentTo($organizer, TargetDrawn::class, fn (TargetDrawn $notification): bool => str_contains((string) $notification->toMail($organizer)->render(), 'Un pull ?'));
    Notification::assertNotSentTo($organizer, TargetMessages::class);
});

test('sending the email again keeps the received address locked', function (): void {
    Notification::fake();
    $draw = createOrganizedDraw('orga@example.com');
    // Only the recap's read receipt proves the address works, and sending it again resets its status
    $draw->recapMail()->markAsReceived();
    $this->travel(10)->minutes();

    fixOrganizer($draw->fresh(), 'orga@example.com')->assertSuccessful();

    $this->travel(10)->minutes();
    fixOrganizer($draw->fresh(), 'orga@exampl.com')->assertStatus(422)->assertJsonPath('errors.email.0', trans('error.fixOrganizer.alreadyReceived'));
    expect($draw->fresh()->organizer_email)->toBe('orga@example.com');
});

test('an unconfirmed address entered again as is is sent again, without the change warning', function (): void {
    Notification::fake();
    $draw = createOrganizedDraw('orga@exampel.com');
    $this->travel(10)->minutes();

    fixOrganizer($draw, 'orga@exampel.com')->assertSuccessful()->assertJsonPath('message', trans('message.resentOrganizer'));
    Notification::assertSentToTimes($draw, OrganizerRecap::class, 1);
    Notification::assertSentOnDemandTimes(OrganizerEmailChanged::class, 0);

    // Not a proof of reception: the address can still be fixed afterwards
    $this->travel(10)->minutes();
    fixOrganizer($draw, 'orga@example.com')->assertSuccessful()->assertJsonPath('message', trans('message.fixedOrganizer'));
    expect($draw->fresh()->organizer_email)->toBe('orga@example.com');
});

test('the fix-organizer command asks for the organizer email when not given', function (): void {
    Notification::fake();
    $draw = createOrganizedDraw('orga@exampel.com');
    $url = URL::signedRoute('dearSanta', ['participant' => $draw->participants->last()->hash]).'#'.base64_encode(DrawCrypt::getIV());

    $this->artisan('secretsanta:fix-organizer', ['url' => $url])
        ->expectsQuestion('What is the correct email of the organizer?', 'orga@example.com')
        ->expectsConfirmation('Change the organizer email from orga@exampel.com to orga@example.com and send their emails again?', 'yes')
        ->assertSuccessful();

    expect($draw->fresh()->organizer_email)->toBe('orga@example.com');
    Notification::assertSentToTimes($draw, OrganizerRecap::class, 1);
});

test('the fix-organizer command changes nothing and sends nothing when not confirmed', function (): void {
    Notification::fake();
    $draw = createOrganizedDraw('orga@exampel.com');
    $url = URL::signedRoute('dearSanta', ['participant' => $draw->participants->last()->hash]).'#'.base64_encode(DrawCrypt::getIV());
    Notification::fake(); // Only what the command sends from here

    $this->artisan('secretsanta:fix-organizer', ['url' => $url, 'email' => 'orga@example.com'])
        ->expectsConfirmation('Change the organizer email from orga@exampel.com to orga@example.com and send their emails again?', 'no')
        ->assertSuccessful();
    $this->artisan('secretsanta:fix-organizer', ['url' => $url, 'email' => 'orga@exampel.com'])
        ->expectsConfirmation('Send the organizer their emails again, to orga@exampel.com?', 'no')
        ->assertSuccessful();

    expect($draw->fresh()->organizer_email)->toBe('orga@exampel.com');
    Notification::assertNothingSent();
});

test('keeping the current address in the fix-organizer command only sends the emails again', function (): void {
    Notification::fake();
    $draw = createServiceDraw(generateParticipants(3));
    $organizer = $draw->participants->first();
    $draw->update(['organizer_name' => $organizer->name, 'organizer_email' => $organizer->email, 'organizer_participant_id' => $organizer->id]);
    // The participant changed their own address since: the organizer address isn't theirs to overwrite it
    $organizer->update(['email' => 'new-'.$organizer->email]);
    $url = URL::signedRoute('dearSanta', ['participant' => $draw->participants->last()->hash]).'#'.base64_encode(DrawCrypt::getIV());

    $this->artisan('secretsanta:fix-organizer', ['url' => $url, 'email' => $draw->organizer_email, '--force' => true])->assertSuccessful();

    expect($organizer->fresh()->email)->toBe('new-'.$draw->organizer_email);
    // Their target email holds the recap: no separate one
    Notification::assertSentToTimes($draw, OrganizerRecap::class, 0);
    Notification::assertSentToTimes($organizer, TargetDrawn::class, 2);
});

test('a link without its secret, of a removed participant or of a draw that ended is refused like an unknown one', function (): void {
    Notification::fake();
    $draw = createServiceDraw(generateParticipants(4));
    $draw->update(['organizer_name' => 'Orga', 'organizer_email' => 'orga@exampel.com']);
    $this->travel(10)->minutes();

    $url = URL::signedRoute('dearSanta', ['participant' => $draw->participants->last()->hash]).'#'.base64_encode(DrawCrypt::getIV());
    $fix = fn (string $url): TestResponse => ajaxPost(URL::route('fixOrganizer.handle'), ['url' => $url, 'email' => 'orga@example.com']);

    // The part after # is the draw secret
    $fix(explode('#', $url, 2)[0])->assertStatus(422)->assertJsonValidationErrors('url');

    $draw->participants->last()->delete();
    $fix($url)->assertStatus(422)->assertJsonValidationErrors('url');

    $ended = Draw::factory()->hasParticipants(3)->expired()->create();
    fixOrganizer($ended, 'orga@example.com')->assertStatus(422)->assertJsonValidationErrors('url');

    Notification::assertSentOnDemandTimes(OrganizerEmailChanged::class, 0);
    expect($draw->fresh()->organizer_email)->toBe('orga@exampel.com');
});
