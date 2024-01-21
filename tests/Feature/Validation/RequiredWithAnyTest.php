<?php

it('passes with nothing', function () {
    $this->assertTrue(
        Validator::make(
            [],
            ['content' => 'required_with_any:users.*.name']
        )->passes()
    );
});

it('passes with empty array', function () {
    $this->assertTrue(
        Validator::make(
            ['users' => []],
            ['content' => 'required_with_any:users.*.name']
        )->passes()
    );
});

it('fails when simply invalid', function () {
    $this->assertFalse(
        Validator::make(
            ['users' => [['name' => 'Foo']]],
            ['content' => 'required_with_any:users.*.name']
        )->passes()
    );
});

it('fails when complex invalid', function () {
    $this->assertFalse(
        Validator::make(
            ['users' => ['list' => [['name' => ['txt' => 'Foo']]]]],
            ['content' => 'required_with_any:users.list.*.name.txt']
        )->passes()
    );
});

it('passes when valid', function () {
    $this->assertTrue(
        Validator::make(
            ['users' => ['list' => [['name' => ['txt' => 'Foo']]]], 'content' => 'foobar'],
            ['content' => 'required_with_any:users.list.*.name.txt']
        )->passes()
    );
});
