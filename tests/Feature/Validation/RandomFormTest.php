<?php

declare(strict_types=1);

it('fails if nothing sent', function (): void {
    ajaxPost('/', ['a' => 'b'])
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'organizer.name', 'organizer.email', 'participants', 'data-expiration', 'title', 'content-email',
        ]);
});

it('needs at least three names', function (): void {
    ajaxPost('/', ['participants' => [
        ['name' => 'toto'],
    ]])
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'organizer.name', 'organizer.email', 'participants', 'participants.0.email', 'title', 'content-email', 'data-expiration',
        ]);

    ajaxPost('/', ['participants' => [
        ['name' => 'toto'],
        ['name' => 'tata'],
    ]])
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'organizer.name', 'organizer.email',
            'participants', 'participants.0.email', 'participants.1.email',
            'title', 'content-email', 'data-expiration',
        ]);
});

it('fails if names are duplicates', function (): void {
    ajaxPost('/', ['participants' => [
        ['name' => 'toto'],
        ['name' => 'toto'],
    ]])
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'organizer.name', 'organizer.email', 'participants',
            'participants.0.name', 'participants.0.email',
            'participants.1.name', 'participants.1.email',
            'title', 'content-email', 'data-expiration',
        ]);
});

it('requires emails', function (): void {
    ajaxPost('/', ['participants' => [
        ['name' => 'toto'],
        ['name' => 'tata'],
        ['name' => 'tutu'],
    ]])
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'organizer.name', 'organizer.email',
            'participants.0.email', 'participants.1.email', 'participants.2.email',
            'title', 'content-email', 'data-expiration',
        ]);

    ajaxPost('/', ['participants' => [
        ['name' => 'toto'],
        ['name' => 'tata', 'email' => 'test@test.com'],
        ['name' => 'tutu'],
    ]])
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'organizer.name', 'organizer.email',
            'participants.0.email', 'participants.2.email',
            'title', 'content-email', 'data-expiration',
        ]);
});

it('needs valid email format', function (): void {
    ajaxPost('/', ['participants' => [
        ['name' => 'toto', 'email' => 'invalidformat'],
        ['name' => 'tata', 'email' => 'test@test.com'],
        ['name' => 'tutu', 'email' => 'tata@test.com'],
    ]])
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'organizer.name', 'organizer.email',
            'participants.0.email',
            'title', 'content-email', 'data-expiration',
        ]);
});

it('needs email body', function (): void {
    ajaxPost('/', ['participants' => [
        ['name' => 'toto', 'email' => 'test@est.com'],
        ['name' => 'tata', 'email' => 'test@test.com'],
        ['name' => 'tutu', 'email' => 'test@test.com'],
    ]])
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'organizer.name', 'organizer.email', 'title', 'content-email', 'data-expiration',
        ]);
});

it('fails if exclusion does not exist', function (): void {
    ajaxPost('/', ['participants' => [
        ['name' => 'toto', 'exclusions' => ['87']],
        ['name' => 'tata'],
        ['name' => 'tutu'],
    ]])
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'organizer.name', 'organizer.email',
            'participants.0.exclusions.0', 'participants.0.email',
            'participants.1.email',
            'participants.2.email',
        ]);
});

it('works with valid exclusion', function (): void {
    ajaxPost('/', ['participants' => [
        ['name' => 'toto', 'exclusions' => ['1']],
        ['name' => 'tata'],
        ['name' => 'tutu'],
    ]])
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'organizer.name', 'organizer.email',
            'participants.0.email',
            'participants.1.email',
            'participants.2.email',
            'title', 'content-email', 'data-expiration',
        ]);
});

it('needs a date in specific format', function (): void {
    ajaxPost('/', ['participants' => [
        ['name' => 'toto', 'email' => 'test@test.com'],
        ['name' => 'tata', 'email' => 'test@test.com'],
        ['name' => 'tutu', 'email' => 'test@test.com'],
    ], 'participant-organizer' => '1', 'data-expiration' => date('Ymd', strtotime('+3 day'))])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['title', 'content-email', 'data-expiration']);
});

it('fails if the expiration is past', function (): void {
    ajaxPost('/', ['participants' => [
        ['name' => 'toto', 'email' => 'test@test.com'],
        ['name' => 'tata', 'email' => 'test@test.com'],
        ['name' => 'tutu', 'email' => 'test@test.com'],
    ], 'participant-organizer' => '1', 'data-expiration' => '1970-08-22'])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['title', 'content-email', 'data-expiration']);
});

it('fails if the expiration is too soon', function (): void {
    ajaxPost('/', ['participants' => [
        ['name' => 'toto', 'email' => 'test@test.com'],
        ['name' => 'tata', 'email' => 'test@test.com'],
        ['name' => 'tutu', 'email' => 'test@test.com'],
    ], 'participant-organizer' => '1', 'data-expiration' => date('Y-m-d')])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['title', 'content-email', 'data-expiration']);
});

it('works if the expiration is far enough', function (): void {
    ajaxPost('/', ['participants' => [
        ['name' => 'toto', 'email' => 'test@test.com'],
        ['name' => 'tata', 'email' => 'test@test.com'],
        ['name' => 'tutu', 'email' => 'test@test.com'],
    ], 'data-expiration' => date('Y-m-d', strtotime('+3 day'))])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['organizer.name', 'organizer.email']);
});

it('bounds the names, the title and the email content', function (): void {
    ajaxPost('/', [
        'organizer' => ['name' => str_repeat('a', 256)],
        'participants' => [['name' => str_repeat('b', 256)]],
        'title' => str_repeat('c', 256),
        'content-email' => str_repeat('d', 10001).'{TARGET}',
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['organizer.name', 'participants.0.name', 'title', 'content-email']);
});

it('explains that the email content must name the target', function (): void {
    ajaxPost('/', ['content-email' => 'no target here'])
        ->assertStatus(422)
        ->assertJsonPath('errors.content-email.0', __('validation.custom.randomform.content.contains_text'));
});
