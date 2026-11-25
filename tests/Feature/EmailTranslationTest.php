<?php

declare(strict_types=1);

use App\Enums\ReportReason;
use App\Models\Draw;
use App\Models\GiftSuggestion;
use App\Notifications\OrganizerRecap;
use App\Notifications\ParticipantEmailCode;
use App\Notifications\ParticipantReported;
use App\Notifications\TargetDrawn;
use App\Notifications\TargetMessages;
use App\Notifications\TargetWithdrawn;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Notification;

// The worker renders the emails in the language of the request (see QueueLocaleTest): none is left in French only
it('renders the emails in the language of the request', function (string $locale, array $expected): void {
    Notification::fake();
    App::setLocale($locale);

    $draw = Draw::factory()->hasParticipants(3)->create();
    $participant = $draw->participants->first();
    // Their target wrote to them and got a gift idea: both are part of the emails
    $participant->target->dearSantas()->create(['mail_body' => 'Hello']);
    $suggestion = new GiftSuggestion(['mail_body' => 'A book']);
    $suggestion->sender()->associate($participant->santa);
    $suggestion->recipient()->associate($participant->target)->save();

    $html = new TargetDrawn()->toMail($participant)->render()
        .new TargetWithdrawn()->toMail($participant->santa)->render()
        .new TargetMessages()->toMail($participant)->render()
        .new ParticipantReported('Zoé', 'zoe@example.com', ReportReason::NotMe)->toMail($draw)->render()
        .new ParticipantReported('Zoé', 'zoe@example.com', ReportReason::NotParticipating)->toMail($draw)->render()
        .new ParticipantEmailCode('Zoé', '012345')->toMail(new AnonymousNotifiable)->render();

    foreach ($expected as $text) {
        expect($html)->toContain($text);
    }

    // No key left untranslated
    expect($html)->not->toContain('emails.');
})->with([
    'fr' => ['fr', ['Vous pouvez écrire à votre Père Noël Secret', 'Secrètement votre', 'Ce code est valable 60 minutes', 'Voici tous les messages que', 'idées cadeaux que d']],
    'en' => ['en', ['You can write to your Secret Santa', 'Secretly yours', 'This code is valid for 60 minutes', 'Here are all the messages', 'gift ideas other participants']],
    'de' => ['de', ['Sie können Ihrem geheimen Weihnachtsmann schreiben', 'Geheimnisvoll Ihr', 'Dieser Code ist 60 Minuten gültig', 'Hier sind alle Nachrichten', 'Geschenkideen, die andere']],
]);

// The organizer's target email holds their recap: nobody to report to, their page has no such button
it('does not ask the organizer to report to the organizer', function (): void {
    Notification::fake();
    $draw = createServiceDraw(generateParticipants(3));
    [$organizer, $other] = $draw->participants;
    $report = e(trans('emails.target_drawn.report'));

    $organizerMail = new TargetDrawn()->toMail($organizer);
    expect((string) $organizerMail->render())->not->toContain($report)
        ->toContain(e(trans('emails.target_drawn.as_organizer')))
        ->and((string) new TargetDrawn()->toMail($other)->render())->toContain($report);
});

// Someone may use another person's address as the organizer's, to spam: they can delete the draw or tell us
it('tells the organizer how to delete a draw they did not make, or report it', function (): void {
    Notification::fake();
    config(['app.email' => 'abuse@example.com']);
    $draw = createServiceDraw(generateParticipants(3));
    [$organizer, $other] = $draw->participants;

    $organizerMail = new TargetDrawn()->toMail($organizer);
    $recap = new OrganizerRecap($draw)->toMail($draw);
    foreach ([$organizerMail, $recap] as $mail) {
        expect((string) $mail->render())->toContain('mailto:abuse@example.com')->toContain(e(trans('organizer.purge.button')))
            ->and(view($mail->view[1], $mail->data())->render())->toContain('abuse@example.com');
    }

    expect((string) new TargetDrawn()->toMail($other)->render())->not->toContain('abuse@example.com');
});

it('writes the text part of the emails as text, without HTML entities', function (): void {
    Notification::fake();
    $draw = createServiceDraw(generateParticipants(3));
    $mail = new TargetDrawn()->toMail($draw->organizer);

    // [html, text] views
    expect(view($mail->view[1], $mail->data())->render())
        ->toContain(trans('emails.target_drawn.do_not_forward'))
        ->not->toContain('&#039;');
});
