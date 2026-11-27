<?php

declare(strict_types=1);

use App\Models\Draw;
use App\Notifications\OrganizerRecap;
use App\Notifications\TargetDrawn;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

test('the draw form edits participants, imports a CSV and suggests email fixes', function (): void {
    $page = visit('/')
        ->assertCount('.participant', 3)
        ->click('#randomForm button[type=submit]')
        ->assertPresent('[dusk=participant2] .is-invalid')
        ->click('.participant-add')
        ->assertCount('.participant', 4)
        ->click('.participant-remove >> nth=-1')
        ->wait(1.5)
        ->assertCount('.participant', 3);

    // ISO-8859-15 file, the 2nd participant excludes the 1st
    $page->click('.participants-import')
        ->attach('#uploadCsv input[type=file]', __DIR__.'/latin15.csv')
        ->click('button:has-text("Importer") >> nth=-1')
        ->assertSeeIn('.ajs-message.ajs-success', 'effectué avec succès')
        ->assertValue('[name="participants[0][name]"]', 'Zoé')
        ->assertValue('[name="participants[1][name]"]', 'Cœur')
        // An imported exclusion keeps the file's direction: one way
        ->assertSeeIn('[dusk=participant1] .exclusion-tag-oneway', 'Zoé')
        ->assertCount('[dusk=participant0] .exclusion-tag', 0);

    // A typed one applies both ways: shown and sent in the other row too
    $page->click('[dusk=participant2] .multiselect')
        ->click('[dusk=participant2] .multiselect__element >> nth=0')
        ->assertCount('[dusk=participant2] select option', 1)
        ->assertSeeIn('[dusk=participant0] .exclusion-tag-derived', 'Max')
        ->assertCount('[dusk=participant0] select option', 1);

    // One way only: the other row neither shows it nor sends it any more
    $page->click('[dusk=participant2] .exclusion-tag-direction')
        ->assertCount('[dusk=participant2] .exclusion-tag-oneway', 1)
        ->assertCount('[dusk=participant2] select option', 1)
        ->assertCount('[dusk=participant0] .exclusion-tag-derived', 0)
        ->assertCount('[dusk=participant0] select option', 0);

    // Then the other direction added in the other row: one way too, each row shows its own
    $page->click('[dusk=participant0] .multiselect')
        ->click('[dusk=participant0] .multiselect__element:has-text("Max")')
        ->assertCount('[dusk=participant0] .exclusion-tag-oneway', 1)
        ->assertCount('[dusk=participant2] .exclusion-tag-oneway', 1)
        ->assertCount('[dusk=participant0] select option', 1);

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
        ->assertCount('.participant', 3)
        ->assertNoJavaScriptErrors();
});

test('the {SANTA} and {TARGET} chips insert the keyword in the message', function (): void {
    $page = visit('/');

    $page->type('#mailContent', 'Salut  !')
        // At the cursor, between the two spaces
        ->script("const t = document.querySelector('#mailContent'); t.focus(); t.setSelectionRange(6, 6);");
    $page->click('.chip--santa')
        ->assertValue('#mailContent', 'Salut {SANTA} !')
        ->click('.chip--target')
        ->assertNoJavaScriptErrors();
    expect($page->script("document.querySelector('#mailContent').value"))->toContain('{TARGET}');
});

test('the event name of the top of the page fills the form', function (): void {
    visit('/')
        ->type('#hero-title', 'Noël chez Martin')
        ->assertValue('[name=title]', 'Noël chez Martin')
        ->assertNoJavaScriptErrors();
});

test('the mail content textarea autosizes and the organizer toggle shows their fields', function (): void {
    $page = visit('/');
    $before = $page->script("document.querySelector('#mailContent').offsetHeight");
    $page->type('#mailContent', str_repeat("a\n", 15));
    expect($page->script("document.querySelector('#mailContent').offsetHeight"))->toBeGreaterThan($before);

    $page->assertNotPresent('[name="organizer[name]"]')
        ->click('.organizer-participates');
    $page->assertPresent('[name="organizer[name]"]')->assertNoJavaScriptErrors();
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
        ->assertVisible('#success-wrapper');
}

test('a non participating organizer gets a separate recap', function (): void {
    Notification::fake();
    $page = visit('/');
    $page->click('.organizer-participates');
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
    $secret = '#'.base64_encode(DrawCrypt::getSecret());
    $dearSanta = URL::signedRoute('dearSanta', ['participant' => $bob->hash]).$secret;
    $aliceDearSanta = URL::signedRoute('dearSanta', ['participant' => $alice->hash]).$secret;

    $santa = visit($dearSanta)
        ->assertSeeIn('.targets-revealed', "n'a pas consulté")
        ->click('.santa-compose button[type=submit]')
        ->assertPresent('textarea.is-invalid')
        ->assertPresent('.no-email')
        ->type('#mailContent', 'Une écharpe ?')
        ->click('.santa-compose button[type=submit]')
        ->assertSeeIn('.email', 'Une écharpe ?')
        ->assertMissing('.no-email')
        // A wrong recipient reports (withdrawing asks for their own name, see below)
        ->click('.report-open')
        ->click('.report-not-me')
        ->assertPresent('.report-sent')
        ->assertNoJavaScriptErrors();
    $santa->refresh()->assertPresent('.report-sent');

    // Bob's address is now refused: only the other failed one can be sent again. Both bounced as unknown
    $draw->participants->take(-2)->each(fn ($p) => $p->mail->forceFill(['delivery_status' => 'error', 'error_code' => '5.1.1'])->save());

    // Alice organizes and takes part: a single page, her panel, holds her participant part too (her
    // participant link from earlier emails leads there), in its own tab. Nothing there she can't use as the organizer
    $org = visit($aliceDearSanta)
        ->assertPathBeginsWith('/org/')
        ->click('.organizer-participation-tab')
        ->assertPresent('#panel-participation #mailContent')
        ->assertNotPresent('#panel-participation .targets-revealed')
        ->assertNotPresent('#panel-participation .email-change')
        ->assertNotPresent('#panel-participation .report')
        ->type('#mailContent', 'Des chaussettes ?')
        ->click('#panel-participation .santa-compose button[type=submit]')
        ->assertSeeIn('#panel-participation .email', 'Des chaussettes ?')
        ->click('#tab-organization')
        ->assertSee('Alice')
        // "À traiter": the report, the bounce with its reason
        ->assertCount('.pg-table .pg-status--warn', 1)
        ->assertSeeIn('.pg-table .pg-status--warn', 'Mauvais destinataire')
        // Bob's report comes before his bounce: only Chloe shows the failure
        ->assertCount('.pg-table .pg-status--err', 1)
        ->assertSeeIn('.todo-error', 'Adresse inexistante');
    $org->click('.pg-table tr.participant-row >> nth=2 >> .participant-actions')
        ->click('.pg-table tr.participant-row >> nth=2 >> .participant-resend')
        // Sent again: no more failure for the one retried
        ->assertCount('.pg-table .pg-status--err', 0);
    Notification::assertSentTimes(TargetDrawn::class, 4);

    // Email fix: an invalid address isn't sent, a valid one is
    $org->click('.pg-table tr.participant-row >> nth=0 >> .participant-actions')
        ->click('.pg-table tr.participant-row >> nth=0 >> .participant-fix')
        ->type('[role=dialog] input', 'pas-un-email')
        ->click('.k-dlg-ok')
        ->assertPresent('[role=dialog]')
        ->type('[role=dialog] input', 'alice2@example.com')
        ->click('.k-dlg-ok')
        ->assertNotPresent('[role=dialog]')
        ->assertSee('alice2@example.com');

    // Event date, location, budget: saved together
    $date = now()->addDays(40)->format('Y-m-d');
    $org->type('#expires_at', $date)
        ->type('#event_location', 'Chez Alice')
        ->click('.day-save')
        ->assertSeeIn('.pg-meta', 'Chez Alice');
    expect($draw->fresh())->expires_at->format('Y-m-d')->toBe($date)->location->toBe('Chez Alice');

    // Add one, then two added and one removed at once
    $org->click('button.participant-add')
        ->assertSeeIn('[role=dialog]', 'a eu lieu aujourd')
        ->type('[role=dialog] input >> nth=0', 'Dan')
        ->type('[role=dialog] input >> nth=1', 'dan@example.com')
        ->click('.k-dlg-ok')
        ->assertSee('Dan')
        ->wait(0.5)->click('.ajs-footer .btn-primary:visible >> nth=-1')
        ->assertSeeIn('.received-count', '/ 4');
    $org->click('.multi-toggle');
    foreach (['eve', 'fred'] as $name) {
        $org->click('button.participant-add')
            ->type('[role=dialog] input >> nth=0', ucfirst($name))
            ->type('[role=dialog] input >> nth=1', "$name@example.com")
            ->click('.k-dlg-ok');
    }

    $org->click('.pg-table tr:has(.participant-name:text-is("Dan")) .participant-actions')
        ->click('.pg-table tr:has(.participant-name:text-is("Dan")) .participant-remove')
        ->assertCount('.pg-table tr.pending-add', 2)
        ->assertCount('.pg-table tr.pending-remove', 1)
        ->click('.multi-validate')
        ->click('.k-dlg-ok')
        ->assertSee('Fred')
        ->wait(0.5)->click('.ajs-footer .btn-primary:visible >> nth=-1')
        ->assertCount('.pg-table tr.participant-row', 5)
        ->assertDontSee('Dan');

    // Reveal, final CSV, participants see it
    $org->assertNotPresent('.pg-table td.target:not(:has(.pg-secret))')
        ->click('.reveal-open')
        // Permanent: needs the confirmation word
        ->assertButtonDisabled('.k-dlg-ok')
        ->type('.k-dlg-word', 'oui')
        ->assertButtonDisabled('.k-dlg-ok')
        ->type('.k-dlg-word', 'révéler')
        ->click('.k-dlg-ok')
        ->assertCount('.pg-table td.target:not(:has(.pg-secret))', 5)
        ->assertButtonEnabled('.csv-final');
    visit($dearSanta)->assertSeeIn('.targets-revealed', 'a consulté');

    // Purge needs the confirmation word
    $org->click('.purge-open')
        ->type('.k-dlg-word', 'nope')
        ->assertButtonDisabled('.k-dlg-ok')
        ->type('.k-dlg-word', 'supprimer')
        ->click('.k-dlg-ok')
        ->assertPresent('.ajs-header:has(.fa-check)')
        ->assertNoJavaScriptErrors();
});

test('withdrawing takes typing your own name, and the organizer can bring them back', function (): void {
    Notification::fake();
    // No exclusions: nothing keeps them from leaving
    $draw = createServiceDraw(generateParticipants(4, withExclusions: false));
    $participant = $draw->participants->last();
    $secret = '#'.base64_encode(DrawCrypt::getSecret());

    $page = visit(URL::signedRoute('dearSanta', ['participant' => $participant->hash]).$secret)
        ->click('.report-open')
        ->click('.withdraw')
        ->assertSeeIn('[role=dialog]', $participant->name);

    // Another word doesn't do
    $page->type('.k-dlg-word', 'Me désister')
        ->assertButtonDisabled('.k-dlg-ok');
    expect($participant->fresh())->not->toBeNull();

    $page->type('.k-dlg-word', $participant->name)
        ->click('.k-dlg-ok')
        ->assertPresent('.ajs-header:has(.fa-check)')
        ->assertNoJavaScriptErrors();
    expect($participant->fresh()->withdrawn_at)->not->toBeNull();

    // Someone else had got the email: the organizer brings them back, at the right address
    visit(URL::signedRoute('organizerPanel', ['draw' => $draw->hash]).$secret)
        ->assertCount('.pg-table tr.participant-row', 3)
        ->assertSeeIn('.withdrawn-participant', $participant->name)
        ->click('.withdrawn-participant .participant-restore')
        ->type('[role=dialog] input', 'right@example.com')
        ->click('.k-dlg-ok')
        ->assertNotPresent('.withdrawn-participant')
        ->assertCount('.pg-table tr.participant-row', 4)
        ->assertNoJavaScriptErrors();
    expect($participant->fresh())->withdrawn_at->toBeNull()->email->toBe('right@example.com');
});

test('static pages and the fix form', function (): void {
    visit(['/faq', '/legal'])->assertNoJavaScriptErrors();

    // Nothing covers the footer: its links get the clicks
    foreach (['/', '/faq'] as $url) {
        expect(visit($url)->script("(() => { const a = document.querySelector('footer a'); a.scrollIntoView({ behavior: 'instant' }); const r = a.getBoundingClientRect(); return a.contains(document.elementFromPoint(r.x + r.width / 2, r.y + r.height / 2)); })()"))->toBeTrue();
    }

    visit('/faq')->click('#question0')->assertVisible('#answer0')->assertNoJavaScriptErrors();
    visit('/nope')->assertPresent('h1.section-title');
    visit('/fix')->click('button[type=submit]')->assertPresent('.is-invalid')->assertNoJavaScriptErrors();
});

test('removing a row keeps the other rows validation state', function (): void {
    $page = visit('/')->click('.participant-add');
    $page->type('[name="participants[0][name]"]', 'Alice')->type('[name="participants[0][email]"]', 'alice@example.com')
        ->type('[name="participants[1][name]"]', 'Bruno')->type('[name="participants[1][email]"]', 'pas-un-email')
        ->script('document.activeElement.blur()');
    $page->type('[name="participants[2][name]"]', 'Chloe')
        ->assertPresent('[name="participants[1][email]"].is-invalid');

    // Chloe moves up a row: she must not inherit Bruno's invalid email
    $page->click('[dusk=participant1] .participant-remove:visible')
        ->wait(1.5)
        ->assertValue('[name="participants[1][name]"]', 'Chloe')
        ->assertNotPresent('[name="participants[1][email]"].is-invalid')
        ->assertNoJavaScriptErrors();
});
