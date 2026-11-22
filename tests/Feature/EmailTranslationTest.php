<?php

declare(strict_types=1);

use App\Models\Draw;
use App\Notifications\ParticipantEmailCode;
use App\Notifications\TargetDrawn;
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

    $html = new TargetDrawn()->toMail($participant)->render()
        .new TargetWithdrawn()->toMail($participant->santa)->render()
        .new ParticipantEmailCode('Zoé', '012345')->toMail(new AnonymousNotifiable)->render();

    foreach ($expected as $text) {
        expect($html)->toContain($text);
    }

    // No key left untranslated
    expect($html)->not->toContain('emails.');
})->with([
    'fr' => ['fr', ['Vous pouvez écrire à votre Père Noël Secret', 'Secrètement votre', 'Ce code est valable 60 minutes']],
    'en' => ['en', ['You can write to your Secret Santa', 'Secretly yours', 'This code is valid for 60 minutes']],
    'de' => ['de', ['Sie können Ihrem geheimen Weihnachtsmann schreiben', 'Geheimnisvoll Ihr', 'Dieser Code ist 60 Minuten gültig']],
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
