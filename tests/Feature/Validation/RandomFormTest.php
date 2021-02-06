<?php

it('fails if nothing sent', function () {
    ajaxPost('/', ['a' => 'b'])
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'participants', 'data-expiration', 'title', 'content-email',
        ]);
});

it('needs at least three names', function () {
    ajaxPost('/', ['participants' => [
            ['name' => 'toto'],
        ]])
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'participants', 'participants.0.email', 'title', 'content-email', 'data-expiration',
        ]);

    ajaxPost('/', ['participants' => [
            ['name' => 'toto'],
            ['name' => 'tata'],
        ]])
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'participants', 'participants.0.email', 'participants.1.email',
            'title', 'content-email', 'data-expiration',
        ]);
});

it('fails if names are duplicates', function () {
    ajaxPost('/', ['participants' => [
            ['name' => 'toto'],
            ['name' => 'toto'],
        ]])
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'participants',
            'participants.0.name', 'participants.0.email',
            'participants.1.name', 'participants.1.email',
            'title', 'content-email', 'data-expiration',
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
            'participants.0.email', 'participants.2.email',
            'title', 'content-email', 'data-expiration',
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
            'participants.0.email',
            'title', 'content-email', 'data-expiration',
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
            'title', 'content-email', 'data-expiration',
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
            'participants.0.email',
            'participants.1.email',
            'participants.2.email',
            'title', 'content-email', 'data-expiration',
        ]);
});

it('fails if the expiration is too soon', function () {
    ajaxPost('/', ['participants' => [
            ['name' => 'toto', 'email' => 'test@test.com'],
            ['name' => 'tata', 'email' => 'test@test.com'],
            ['name' => 'tutu', 'email' => 'test@test.com'],
        ], 'data-expiration' => date('Y-m-d')])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['title', 'content-email', 'data-expiration']);
});

it('works if the expiration is far enough', function () {
    ajaxPost('/', ['participants' => [
            ['name' => 'toto', 'email' => 'test@test.com'],
            ['name' => 'tata', 'email' => 'test@test.com'],
            ['name' => 'tutu', 'email' => 'test@test.com'],
        ], 'data-expiration' => date('Y-m-d', strtotime('+3 day'))])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['title', 'content-email']);
});