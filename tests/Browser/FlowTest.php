<?php

declare(strict_types=1);

use App\Models\Draw;
use App\Notifications\OrganizerRecap;
use App\Notifications\TargetDrawn;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

test('the draw form edits participants, imports a CSV and suggests email fixes', function (): void {
    $page = visit('/')
        ->assertCount('tr.participant', 3)
        ->click('#randomForm button[type=submit]')
        ->assertPresent('tr.participant:nth-child(3) .is-invalid')
        ->click('.participant-add')
        ->assertCount('tr.participant', 4)
        ->click('.participant-remove >> nth=-1')
        ->wait(1.5)
        ->assertCount('tr.participant', 3);

    // ISO-8859-15 file, the 2nd participant excludes the 1st
    $page->click('.participants-import')
        ->attach('#uploadCsv input[type=file]', __DIR__.'/latin15.csv')
        ->click('button:has-text("Importer") >> nth=-1')
        ->assertSeeIn('.ajs-message.ajs-success', 'effectué avec succès')
        ->assertValue('[name="participants[0][name]"]', 'Zoé')
        ->assertValue('[name="participants[1][name]"]', 'Cœur')
        // An imported exclusion keeps the file's direction: one way
        ->assertSeeIn('tr.participant:nth-child(2) .exclusion-tag-oneway', 'Zoé')
        ->assertCount('tr.participant:nth-child(1) .exclusion-tag', 0);

    // A typed one applies both ways: shown and sent in the other row too
    $page->click('tr.participant:nth-child(3) .multiselect')
        ->click('tr.participant:nth-child(3) .multiselect__element >> nth=0')
        ->assertCount('tr.participant:nth-child(3) select option', 1)
        ->assertSeeIn('tr.participant:nth-child(1) .exclusion-tag-derived', 'Max')
        ->assertCount('tr.participant:nth-child(1) select option', 1);

    // One way only: the other row neither shows it nor sends it any more
    $page->click('tr.participant:nth-child(3) .exclusion-tag-direction')
        ->assertCount('tr.participant:nth-child(3) .exclusion-tag-oneway', 1)
        ->assertCount('tr.participant:nth-child(3) select option', 1)
        ->assertCount('tr.participant:nth-child(1) .exclusion-tag-derived', 0)
        ->assertCount('tr.participant:nth-child(1) select option', 0);

    // Then the other direction added in the other row: one way too, each row shows its own
    $page->click('tr.participant:nth-child(1) .multiselect')
        ->click('tr.participant:nth-child(1) .multiselect__element:has-text("Max")')
        ->assertCount('tr.participant:nth-child(1) .exclusion-tag-oneway', 1)
        ->assertCount('tr.participant:nth-child(3) .exclusion-tag-oneway', 1)
        ->assertCount('tr.participant:nth-child(1) select option', 1);

    $page->type('[name="participants[0][email]"]', 'zoe@gmial.com')
        ->click('.email-suggest a')
        ->assertValue('[name="participants[0][email]"]', 'zoe@gmail.com')
        ->assertNotPresent('.email-suggest')
        ->assertNoJavaScriptErrors();
});

test('a CSV with a single participant still leaves three rows to fill', function (): void {
    visit('/')
        ->click('.participants-import')
        ->attach('#uploadCsv input[type=file]', __DIR__.'/one-participant.csv')
        ->click('button:has-text("Importer") >> nth=-1')
        ->assertSeeIn('.ajs-message.ajs-success', 'effectué avec succès')
        ->assertValue('[name="participants[0][name]"]', 'Ana')
        ->assertCount('tr.participant', 3)
        ->assertNoJavaScriptErrors();
});

test('the {TARGET} tip link copies the keyword', function (): void {
    $page = visit('/');
    // Headless Chromium may refuse clipboard writes (focus, permission): record what is copied instead
    $page->script("Object.defineProperty(navigator, 'clipboard', { configurable: true, value: { writeText: text => { window.copied = text; return Promise.resolve(); } } })");

    $page->click('a.link >> nth=-1')
        ->assertSeeIn('.ajs-message.ajs-success', 'Copié')
        ->assertNoJavaScriptErrors();
    expect($page->script('window.copied'))->toBe('{TARGET}');
});

test('the mail content textarea autosizes and the organizer toggle shows their fields', function (): void {
    $page = visit('/');
    $before = $page->script("document.querySelector('#mailContent').offsetHeight");
    $page->type('#mailContent', str_repeat("a\n", 15));
    expect($page->script("document.querySelector('#mailContent').offsetHeight"))->toBeGreaterThan($before);

    $page->assertNotPresent('#organizer')
        ->script("document.querySelector('.organizerToggle input').click()");
    $page->assertPresent('#organizer')->assertNoJavaScriptErrors();
});

function submitDraw($page, array $names, string $title = 'Noel'): void
{
    foreach ($names as $i => $name) {
        $page->type("[name=\"participants[$i][name]\"]", ucfirst($name))
            ->type("[name=\"participants[$i][email]\"]", "$name@example.com");
    }

    $page->type('[name=title]', $title)
        ->type('#mailContent', 'Salut {SANTA}, offre a {TARGET}')
        ->type('[name=data-expiration]', now()->addDays(5)->format('Y-m-d'))
        ->click('#randomForm button[type=submit]')
        ->assertSee('envoy');
}

test('a non participating organizer gets a separate recap', function (): void {
    Notification::fake();
    $page = visit('/');
    $page->script("document.querySelector('.organizerToggle input').click()");
    $page->type('[name="organizer[name]"]', 'Orga')->type('[name="organizer[email]"]', 'orga@example.com');
    submitDraw($page, ['gus', 'hugo', 'ines']);

    Notification::assertSentTimes(OrganizerRecap::class, 1);
    Notification::assertCount(4);
});

test('the full flow: draw, dearSanta report, organizer panel with their participant part', function (): void {
    Notification::fake();
    submitDraw(visit('/'), ['alice', 'bob', 'chloe']);

    // A participating organizer only gets their target email (with the organizer block)
    Notification::assertSentTimes(OrganizerRecap::class, 0);
    Notification::assertSentTimes(TargetDrawn::class, 3);

    $draw = Draw::query()->latest('id')->firstOrFail();
    [$alice, $bob] = $draw->participants;
    $iv = '#'.base64_encode(DrawCrypt::getIV());
    $dearSanta = URL::signedRoute('dearSanta', ['participant' => $bob->hash]).$iv;
    $aliceDearSanta = URL::signedRoute('dearSanta', ['participant' => $alice->hash]).$iv;

    $santa = visit($dearSanta)
        ->assertSeeIn('.targets-revealed', 'pas été consultée')
        ->click('button[type=submit]')
        ->assertPresent('textarea.is-invalid')
        ->assertPresent('tr.no-email')
        ->type('#mailContent', 'Une écharpe ?')
        ->click('button[type=submit]')
        ->assertSeeIn('tr.email', 'Une écharpe ?')
        ->assertMissing('tr.no-email')
        // Who they are first: a wrong recipient reports, only the right person may withdraw
        ->assertNotPresent('.withdraw')
        ->click('.report-open')
        ->assertSeeIn('.report-identity', 'Bob')
        ->click('.report-not-me')
        ->assertPresent('.report-sent')
        ->assertNoJavaScriptErrors();
    $santa->refresh()->assertPresent('.report-sent');

    // Bob's address is now refused: the bulk resend only retries the other failed one
    $draw->participants->take(-2)->each(fn ($p) => $p->mail->forceFill(['delivery_status' => 'error'])->save());

    // Alice organizes and takes part: a single page, her panel, holds her participant part too (her
    // participant link from earlier emails leads there). Nothing there she can't use as the organizer
    $org = visit($aliceDearSanta)
        ->assertPathBeginsWith('/org/')
        ->assertPresent('.organizer-participation #mailContent')
        ->assertNotPresent('.organizer-participation .targets-revealed')
        ->assertNotPresent('.organizer-participation .email-change')
        ->assertNotPresent('.organizer-participation .report')
        ->type('#mailContent', 'Des chaussettes ?')
        ->click('.organizer-participation button[type=submit]')
        ->assertSeeIn('.organizer-participation tr.email', 'Des chaussettes ?')
        ->assertSee('Alice')
        ->assertCount('.participant-report', 1)
        ->assertSeeIn('.participant-report', 'Mauvais destinataire')
        ->click('button.btn-warning:has(.fa-redo)')
        ->assertButtonDisabled('button.btn-warning:has(.fa-redo)');
    Notification::assertSentTimes(TargetDrawn::class, 4);

    // Email edit: invalid blocks the submit, valid is saved
    $org->click('button:has(.fa-edit) >> nth=0')
        ->type('input[name=input] >> nth=0', 'pas-un-email')
        ->script('document.activeElement.blur()');
    $org->assertPresent('input[name=input].is-invalid')
        ->assertPresent('.invalid-tooltip')
        ->assertDisabled('button:has(.fa-check-circle) >> nth=0')
        ->click('button:has(.fa-times-circle) >> nth=0')
        ->click('button:has(.fa-edit) >> nth=0')
        ->type('input[name=input] >> nth=0', 'alice2@example.com')
        ->click('button:has(.fa-check-circle) >> nth=0')
        ->assertPresent('[data-state=viewUpdated]');

    // Event date: a future one is saved, a past one is refused and the input restored
    $date = now()->addDays(40)->format('Y-m-d');
    $org->type('#expires_at', $date)
        ->wait(1)
        ->type('#expires_at', '2020-01-01')
        ->assertPresent('.ajs-content:visible')
        ->click('.ajs-footer .btn-primary:visible')
        ->assertValue('#expires_at', $date);
    expect($draw->fresh()->expires_at->format('Y-m-d'))->toBe($date);

    // Add one, then two added and one removed at once
    $org->type('.participant-add-name', 'Dan')->type('.participant-add-email', 'dan@example.com')
        ->click('button.participant-add')
        ->assertSeeIn('.ajs-content:visible .alert-warning', 'a eu lieu aujourd')
        ->wait(0.5)->click('.ajs-footer .btn-primary:visible >> nth=-1')
        ->assertSee('Dan')
        ->wait(0.5)->click('.ajs-footer .btn-primary:visible >> nth=-1')
        ->assertSeeIn('.received-count', '/4');
    $org->click('.multi-toggle');
    foreach (['eve', 'fred'] as $name) {
        $org->type('.participant-add-name', ucfirst($name))->type('.participant-add-email', "$name@example.com")
            ->click('button.participant-add');
    }

    $org->click('tbody tr:has(td:text-is("Dan")) .participant-remove')
        ->assertCount('tr.pending-add', 2)
        ->assertCount('tr.pending-remove', 1)
        ->click('.multi-validate')
        ->wait(0.5)->click('.ajs-footer .btn-primary:visible >> nth=-1')
        ->assertSee('Fred')
        ->wait(0.5)->click('.ajs-footer .btn-primary:visible >> nth=-1')
        ->assertCount('tbody tr:not(.pending-add):not(.organizer-participation *)', 5)
        ->assertDontSee('Dan');

    // Reveal, final CSV, participants see it
    $org->assertNotPresent('td.target')
        ->click('button:has-text("Révéler les cibles")')
        // Permanent: needs the confirmation word too
        ->click('.ajs-footer .btn-primary:visible')
        ->assertVisible('.ajs-content:visible input')
        ->assertNotPresent('td.target')
        ->type('.ajs-content:visible input', 'Révéler les cibles')
        // alertify ignores a second button click within 200 ms
        ->wait(0.5)->click('.ajs-footer .btn-primary:visible')
        ->assertCount('td.target', 5)
        ->assertEnabled('button.btn-primary:has(.fa-download) >> nth=-1');
    visit($dearSanta)->assertSeeIn('.targets-revealed', 'a consulté');

    // Purge needs the confirmation word
    $org->click('button:has-text("Supprimer tout")')
        ->type('.ajs-content:visible input', 'nope')
        ->click('.ajs-footer .btn-primary:visible')
        ->assertVisible('.ajs-content:visible input');
    $word = Str::between($org->text('.ajs-content:visible'), '"', '"');
    $org->type('.ajs-content:visible input', $word)
        ->click('.ajs-footer .btn-primary:visible')
        ->assertPresent('.ajs-header:has(.fa-check)')
        ->assertNoJavaScriptErrors();
});

test('withdrawing takes saying who you are, then typing your own name, and the organizer can bring them back', function (): void {
    Notification::fake();
    // No exclusions: nothing keeps them from leaving
    $draw = createServiceDraw(generateParticipants(4, withExclusions: false));
    $participant = $draw->participants->last();
    $iv = '#'.base64_encode(DrawCrypt::getIV());

    $page = visit(URL::signedRoute('dearSanta', ['participant' => $participant->hash]).$iv)
        ->click('.report-open')
        ->click('.report-its-me')
        ->click('.withdraw')
        ->assertSeeIn('.ajs-content:visible', $participant->name);

    // Another word doesn't do
    $page->type('.ajs-content:visible input', 'Me désister')
        ->click('.ajs-footer .btn-primary:visible')
        ->assertVisible('.ajs-content:visible input');
    expect($participant->fresh())->not->toBeNull();

    $page->type('.ajs-content:visible input', $participant->name)
        ->wait(0.5)->click('.ajs-footer .btn-primary:visible')
        ->assertPresent('.ajs-header:has(.fa-check)')
        ->assertNoJavaScriptErrors();
    expect($participant->fresh()->withdrawn_at)->not->toBeNull();

    // Someone else had got the email: the organizer brings them back, at the right address
    visit(URL::signedRoute('organizerPanel', ['draw' => $draw->hash]).$iv)
        ->assertCount('tbody tr:not(.organizer-participation *)', 3)
        ->assertSeeIn('.withdrawn-participant', $participant->name)
        ->click('.participant-restore')
        ->type('.ajs-content:visible input', 'right@example.com')
        ->wait(0.5)->click('.ajs-footer .btn-primary:visible')
        ->assertNotPresent('.withdrawn-participant')
        ->assertCount('tbody tr:not(.organizer-participation *)', 4)
        ->assertNoJavaScriptErrors();
    expect($participant->fresh())->withdrawn_at->toBeNull()->email->toBe('right@example.com');
});

test('static pages and the fix form', function (): void {
    visit(['/faq', '/legal'])->assertNoJavaScriptErrors();

    // Nothing covers the footer: its links get the clicks
    foreach (['/', '/faq'] as $url) {
        expect(visit($url)->script("(() => { const a = document.querySelector('footer a'); a.scrollIntoView({ behavior: 'instant' }); const r = a.getBoundingClientRect(); return document.elementFromPoint(r.x + r.width / 2, r.y + r.height / 2) === a; })()"))->toBeTrue();
    }

    visit('/faq')->click('#question0')->assertVisible('#answer0')->assertNoJavaScriptErrors();
    visit('/nope')->assertPresent('h1.section-title');
    visit('/fix')->click('button[type=submit]')->assertPresent('.is-invalid')->assertNoJavaScriptErrors();
});

test('a refused copy says so instead of "copied"', function (): void {
    $page = visit('/');
    $page->script("Object.defineProperty(navigator, 'clipboard', { configurable: true, value: { writeText: () => Promise.reject(new DOMException('Write permission denied.', 'NotAllowedError')) } })");

    $page->click('a.link >> nth=-1')
        ->assertSeeIn('.ajs-message.ajs-error', 'Copie impossible')
        ->assertDontSee('Copié dans le presse papier')
        ->assertNoJavaScriptErrors();
});

test('removing a row keeps the other rows validation state', function (): void {
    $page = visit('/')->click('.participant-add');
    $page->type('[name="participants[0][name]"]', 'Alice')->type('[name="participants[0][email]"]', 'alice@example.com')
        ->type('[name="participants[1][name]"]', 'Bruno')->type('[name="participants[1][email]"]', 'pas-un-email')
        ->script('document.activeElement.blur()');
    $page->type('[name="participants[2][name]"]', 'Chloe')
        ->assertPresent('[name="participants[1][email]"].is-invalid');

    // Chloe moves up a row: she must not inherit Bruno's invalid email
    $page->click('tr.participant:nth-child(2) .participant-remove')
        ->wait(1.5)
        ->assertValue('[name="participants[1][name]"]', 'Chloe')
        ->assertNotPresent('[name="participants[1][email]"].is-invalid')
        ->assertNoJavaScriptErrors();
});
