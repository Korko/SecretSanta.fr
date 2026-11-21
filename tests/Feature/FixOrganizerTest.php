<?php

declare(strict_types=1);

use App\Enums\DeliveryStatus;
use App\Models\Draw;
use App\Notifications\OrganizerEmailChanged;
use App\Notifications\OrganizerRecap;
use App\Notifications\TargetDrawn;
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
    // Once at the draw, once now
    Notification::assertSentToTimes($organizer, TargetDrawn::class, 2);
    Notification::assertSentToTimes($draw, OrganizerRecap::class, 1);
    Notification::assertSentOnDemandTimes(OrganizerEmailChanged::class, 0);
    expect($organizer->fresh()->email)->toBe($organizer->email);
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
