<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Validator;

it('passes with nothing', function (): void {
    test()->assertTrue(
        Validator::make(
            [],
            ['content' => ['required_with_any:users.*.name']]
        )->passes()
    );
});

it('passes with empty array', function (): void {
    test()->assertTrue(
        Validator::make(
            ['users' => []],
            ['content' => ['required_with_any:users.*.name']]
        )->passes()
    );
});

it('fails when simply invalid', function (): void {
    test()->assertFalse(
        Validator::make(
            ['users' => [['name' => 'Foo']]],
            ['content' => ['required_with_any:users.*.name']]
        )->passes()
    );
});

it('fails when complex invalid', function (): void {
    test()->assertFalse(
        Validator::make(
            ['users' => ['list' => [['name' => ['txt' => 'Foo']]]]],
            ['content' => ['required_with_any:users.list.*.name.txt']]
        )->passes()
    );
});

it('passes when valid', function (): void {
    test()->assertTrue(
        Validator::make(
            ['users' => ['list' => [['name' => ['txt' => 'Foo']]]], 'content' => 'foobar'],
            ['content' => ['required_with_any:users.list.*.name.txt']]
        )->passes()
    );
});
