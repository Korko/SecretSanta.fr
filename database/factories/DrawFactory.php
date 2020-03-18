<?php

$factory->define(App\Draw::class, function (Faker\Generator $faker) {
    return [
        'mail_title' => $faker->sentence,
        'mail_body'  => $faker->text,
        'expires_at' => $faker->dateTimeBetween('+1 day', '+1 month'),
    ];
});

$factory->state(App\Draw::class, 'expired', function ($faker) {
    return [
        'expires_at' => $faker->dateTime('-1 hour'),
    ];
});
