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

    $html = (string) new TargetDrawn()->toMail($participant)->render()
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
