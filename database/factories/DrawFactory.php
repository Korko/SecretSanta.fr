<?php

$factory->define(App\Draw::class, function (Faker\Generator $faker) {
    return [
        'email_title'     => $faker->sentence,
        'email_body'      => $faker->text,
        'expiration'      => $faker->dateTimeBetween('+1 day', '+1 month'),
        'dear_santa'      => $faker->boolean,
        'challenge'       => $faker->text,
    ];
});

$factory->state(App\Draw::class, 'expired', function ($faker) {
    return [
        'expiration' => $faker->dateTime('-1 hour'),
    ];
});
