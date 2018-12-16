<?php

$factory->define(App\Draw::class, function (Faker\Generator $faker) {
    return [
        'email_title'        => $faker->sentence,
        'email_body'         => $faker->text,
        'organizer_name'     => $faker->name,
        'organizer_email'    => $faker->unique()->safeEmail,
        'expiration'         => $faker->dateTimeBetween('+1 day', '+1 month'),
        'dear_santa_draw_id' => null,
    ];
});

$factory->state(App\Draw::class, 'expired', function ($faker) {
    return [
        'expiration' => $faker->dateTime('-1 hour'),
    ];
});
