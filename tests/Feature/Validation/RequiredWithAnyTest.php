<?php

it('passes with nothing', function () {
    assertTrue(
        Validator::make(
            [],
            ['content' => 'required_with_any:users.*.name']
        )->passes()
    );
});

it('passes with empty array', function () {
    assertTrue(
        Validator::make(
            ['users' => []],
            ['content' => 'required_with_any:users.*.name']
        )->passes()
    );
});

it('fails when simply invalid', function () {
    assertFalse(
        Validator::make(
            ['users' => [['name' => 'Foo']]],
            ['content' => 'required_with_any:users.*.name']
        )->passes()
    );
});

it('fails when complex invalid', function () {
    assertFalse(
        Validator::make(
            ['users' => ['list' => [['name' => ['txt' => 'Foo']]]]],
            ['content' => 'required_with_any:users.list.*.name.txt']
        )->passes()
    );
});

it('passes when valid', function () {
    assertTrue(
        Validator::make(
            ['users' => ['list' => [['name' => ['txt' => 'Foo']]]], 'content' => 'foobar'],
            ['content' => 'required_with_any:users.list.*.name.txt']
        )->passes()
    );
});