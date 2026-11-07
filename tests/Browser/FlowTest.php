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
        ->assertSeeIn('tr.participant:nth-child(2) .multiselect__tag', 'Zoé');

    $page->click('tr.participant:nth-child(3) .multiselect')
        ->click('tr.participant:nth-child(3) .multiselect__element >> nth=0')
        ->assertCount('tr.participant:nth-child(3) select option', 1);

    $page->type('[name="participants[0][email]"]', 'zoe@gmial.com')
        ->click('.email-suggest a')
        ->assertValue('[name="participants[0][email]"]', 'zoe@gmail.com')
        ->assertNotPresent('.email-suggest')
        ->assertNoJavaScriptErrors();
});

test('the {TARGET} tip link copies the keyword', function (): void {
    visit('/')
        ->click('a.link >> nth=-1')
        ->assertSeeIn('.ajs-message.ajs-success', 'Copié')
        ->assertNoJavaScriptErrors();
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

    Notification::assertSentOnDemandTimes(OrganizerRecap::class, 1);
    Notification::assertCount(4);
});

test('the full flow: draw, dearSanta report, organizer panel', function (): void {
    Notification::fake();
    submitDraw(visit('/'), ['alice', 'bob', 'chloe']);

    // A participating organizer only gets their target email (with the organizer block)
    Notification::assertSentOnDemandTimes(OrganizerRecap::class, 0);
    Notification::assertSentTimes(TargetDrawn::class, 3);

    $draw = Draw::query()->latest('id')->firstOrFail();
    [$alice, $bob] = $draw->participants;
    $iv = '#'.base64_encode(DrawCrypt::getIV());
    $dearSanta = URL::signedRoute('dearSanta', ['participant' => $bob->hash]).$iv;
    $panel = URL::signedRoute('organizerPanel', ['draw' => $draw->hash]).$iv;

    $santa = visit($dearSanta)
        ->assertSeeIn('.targets-revealed', 'pas été consultée')
        ->click('button[type=submit]')
        ->assertPresent('textarea.is-invalid')
        ->click('.report-not-me')
        ->assertPresent('.report-sent');
    $santa->refresh()->assertPresent('.report-sent');

    // Bob's address is now refused: the bulk resend only retries the other failed one
    $draw->participants->take(-2)->each(fn ($p) => $p->mail->forceFill(['delivery_status' => 'error'])->save());

    $org = visit($panel)
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
        ->assertSeeIn('.ajs-content:visible .alert-warning', 'il y a 0 jour')
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
        ->assertCount('tbody tr:not(.pending-add)', 5)
        ->assertDontSee('Dan');

    // Reveal, final CSV, participants see it
    $org->assertNotPresent('td.target')
        ->click('button:has-text("Révéler les cibles")')
        ->click('.ajs-footer .btn-primary:visible')
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

test('static pages and the fix form', function (): void {
    visit(['/faq', '/legal'])->assertNoJavaScriptErrors();
    visit('/nope')->assertPresent('h1.section-title');
    visit('/fix')->click('button[type=submit]')->assertPresent('.is-invalid')->assertNoJavaScriptErrors();
});
