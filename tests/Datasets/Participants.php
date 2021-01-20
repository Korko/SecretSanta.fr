<?php

// Should return an array of sets which is an array of arguments and should also be an array of participants which is an array of fields
// So return [[[['name' => '']]]]

// Invalid list when there's no solution
dataset('invalid participants list', function () {
    return [
        [[
            ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => [2]],
            ['name' => uniqid(), 'email' => 'test2@test.com', 'exclusions' => [0, 2]],
            ['name' => uniqid(), 'email' => 'test3@test.com', 'exclusions' => [1]],
        ]],
    ];
});

// Valid list where a solution is possible
dataset('valid participants list', function () {
    return [
        [[
            ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => []],
            ['name' => uniqid(), 'email' => 'test2@test.com', 'exclusions' => []],
            ['name' => uniqid(), 'email' => 'test3@test.com', 'exclusions' => []],
        ]],
    ];
});

// Valid list where there's only one solution
dataset('unique participants list', function () {
    return [
        [
            [
                ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => [2]],
                ['name' => uniqid(), 'email' => 'test2@test.com', 'exclusions' => [0]],
                ['name' => uniqid(), 'email' => 'test3@test.com', 'exclusions' => [1]],
            ],
            [1, 2, 0]
        ],
    ];
});