<?php

it('fails if nothing sent', function () {
    ajaxPost(URL::route('form.process'), ['a' => 'b'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'organizer.name', 'organizer.email', 'participants', 'title', 'content',
        ]);
});

it('needs at least three names', function () {
    ajaxPost(URL::route('form.process'), ['participants' => [
        ['name' => 'toto'],
    ]])
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'organizer.name', 'organizer.email', 'participants', 'participants.0.email', 'title', 'content',
        ]);

    ajaxPost(URL::route('form.process'), ['participants' => [
        ['name' => 'toto'],
        ['name' => 'tata'],
    ]])
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'organizer.name', 'organizer.email',
            'participants', 'participants.0.email', 'participants.1.email',
            'title', 'content',
        ]);
});

it('fails if names are duplicates', function () {
    ajaxPost(URL::route('form.process'), ['participants' => [
        ['name' => 'toto'],
        ['name' => 'toto'],
    ]])
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'organizer.name', 'organizer.email', 'participants',
            'participants.0.name', 'participants.0.email',
            'participants.1.name', 'participants.1.email',
            'title', 'content',
        ]);
});

it('requires emails', function () {
    ajaxPost(URL::route('form.process'), ['participants' => [
        ['name' => 'toto'],
        ['name' => 'tata'],
        ['name' => 'tutu'],
    ]])
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'organizer.name', 'organizer.email',
            'participants.0.email', 'participants.1.email', 'participants.2.email',
            'title', 'content',
        ]);

    ajaxPost(URL::route('form.process'), ['participants' => [
        ['name' => 'toto'],
        ['name' => 'tata', 'email' => 'test@test.com'],
        ['name' => 'tutu'],
    ]])
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'organizer.name', 'organizer.email',
            'participants.0.email', 'participants.2.email',
            'title', 'content',
        ]);
});

it('needs valid email format', function () {
    ajaxPost(URL::route('form.process'), ['participants' => [
        ['name' => 'toto', 'email' => 'invalidformat'],
        ['name' => 'tata', 'email' => 'test@test.com'],
        ['name' => 'tutu', 'email' => 'tata@test.com'],
    ]])
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'organizer.name', 'organizer.email',
            'participants.0.email',
            'title', 'content',
        ]);
});

it('needs email body', function () {
    ajaxPost(URL::route('form.process'), ['participants' => [
        ['name' => 'toto', 'email' => 'test@est.com'],
        ['name' => 'tata', 'email' => 'test@test.com'],
        ['name' => 'tutu', 'email' => 'test@test.com'],
    ]])
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'organizer.name', 'organizer.email', 'title', 'content',
        ]);
});

it('fails if exclusion does not exist', function () {
    ajaxPost(URL::route('form.process'), ['participants' => [
        ['name' => 'toto', 'exclusions' => ['87']],
        ['name' => 'tata'],
        ['name' => 'tutu'],
    ]])
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'organizer.name', 'organizer.email',
            'participants.0.exclusions.0', 'participants.0.email',
            'participants.1.email',
            'participants.2.email',
        ]);
});

it('works with valid exclusion', function () {
    ajaxPost(URL::route('form.process'), ['participants' => [
        ['name' => 'toto', 'exclusions' => ['1']],
        ['name' => 'tata'],
        ['name' => 'tutu'],
    ]])
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'organizer.name', 'organizer.email',
            'participants.0.email',
            'participants.1.email',
            'participants.2.email',
            'title', 'content',
        ]);
});
