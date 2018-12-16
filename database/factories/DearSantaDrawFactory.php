<?php

$factory->define(App\DearSantaDraw::class, function (Faker\Generator $faker) {
    return [
        'expiration' => $faker->dateTimeBetween('+1 day', '+1 month'),
    ];
});

$factory->state(App\DearSantaDraw::class, 'expired', function ($faker) {
    return [
        'expiration' => $faker->dateTime('-1 hour'),
    ];
});
