<?php

it('fails if nothing sent', function () {
    ajaxPost('/', ['a' => 'b'])
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'organizer.name', 'organizer.email', 'participants', 'title', 'content',
        ]);
});

it('needs at least three names', function () {
    ajaxPost('/', ['participants' => [
            ['name' => 'toto'],
        ]])
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'organizer.name', 'organizer.email', 'participants', 'participants.0.email', 'title', 'content',
        ]);

    ajaxPost('/', ['participants' => [
            ['name' => 'toto'],
            ['name' => 'tata'],
        ]])
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'organizer.name', 'organizer.email',
            'participants', 'participants.0.email', 'participants.1.email',
            'title', 'content',
        ]);
});

it('fails if names are duplicates', function () {
    ajaxPost('/', ['participants' => [
            ['name' => 'toto'],
            ['name' => 'toto'],
        ]])
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'organizer.name', 'organizer.email', 'participants',
            'participants.0.name', 'participants.0.email',
            'participants.1.name', 'participants.1.email',
            'title', 'content',
        ]);
});

it('requires emails', function () {
    ajaxPost('/', ['participants' => [
            ['name' => 'toto'],
            ['name' => 'tata'],
            ['name' => 'tutu'],
        ]])
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'organizer.name', 'organizer.email',
            'participants.0.email', 'participants.1.email', 'participants.2.email',
            'title', 'content',
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
            'title', 'content',
        ]);
});

it('needs valid email format', function () {
    ajaxPost('/', ['participants' => [
            ['name' => 'toto', 'email' => 'invalidformat'],
            ['name' => 'tata', 'email' => 'test@test.com'],
            ['name' => 'tutu', 'email' => 'tata@test.com'],
        ]])
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'organizer.name', 'organizer.email',
            'participants.0.email',
            'title', 'content',
        ]);
});

it('needs email body', function () {
    ajaxPost('/', ['participants' => [
            ['name' => 'toto', 'email' => 'test@est.com'],
            ['name' => 'tata', 'email' => 'test@test.com'],
            ['name' => 'tutu', 'email' => 'test@test.com'],
        ]])
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'organizer.name', 'organizer.email', 'title', 'content',
        ]);
});

it('fails if exclusion does not exist', function () {
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

it('works with valid exclusion', function () {
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
            'title', 'content',
        ]);
});