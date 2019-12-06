<?php

$factory->define(App\Draw::class, function (Faker\Generator $faker) {
    return [
        'email_title'     => $faker->sentence,
        'email_body'      => $faker->text,
        'expires_at'      => $faker->dateTimeBetween('+1 day', '+1 month'),
        'challenge'       => $faker->text,
    ];
});

$factory->state(App\Draw::class, 'expired', function ($faker) {
    return [
        'expires_at' => $faker->dateTime('-1 hour'),
    ];
});
