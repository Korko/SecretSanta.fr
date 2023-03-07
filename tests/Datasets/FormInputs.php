<?php

use Illuminate\Support\Collection;
use function Pest\Faker\faker;

// Invalid list when there's no solution
dataset('invalid participants list', function () {
    yield [
        [
            ['name' => faker()->unique()->name, 'email' => faker()->safeEmail, 'exclusions' => [2]],
            ['name' => faker()->unique()->name, 'email' => faker()->safeEmail, 'exclusions' => [0, 2]],
            ['name' => faker()->unique()->name, 'email' => faker()->safeEmail, 'exclusions' => [1]],
        ],
    ];
});

// Valid list where a solution is possible
dataset('participants list', function () {
    yield [Collection::times(5, function () {
        return ['name' => faker()->unique()->name, 'email' => faker()->safeEmail, 'exclusions' => []];
    })->toArray()];
});

// Valid list where there's only one solution
dataset('unique participants list', function () {
    yield [
        [
            ['name' => faker()->unique()->name, 'email' => faker()->safeEmail, 'exclusions' => [2]],
            ['name' => faker()->unique()->name, 'email' => faker()->safeEmail, 'exclusions' => [0]],
            ['name' => faker()->unique()->name, 'email' => faker()->safeEmail, 'exclusions' => [1]],
        ],
        [1, 2, 0],
    ];
});

// Huge valid list where a solution is possible
dataset('huge participants list', function () {
    $faker = faker(); // Keep a single Faker instance to use the "unique" modifier correctly
    yield 'huge list' => [Collection::times(3000, function () use ($faker) {
        return ['name' => $faker->unique()->name, 'email' => $faker->safeEmail, 'exclusions' => []];
    })->toArray()];
});

dataset('validated participants list', function () {
    $participants = Collection::times(3, function () {
        return ['name' => faker()->unique()->name, 'email' => faker()->safeEmail, 'exclusions' => []];
    })->toArray();

    yield [$participants, fn () => createServiceDraw($participants)];
});
