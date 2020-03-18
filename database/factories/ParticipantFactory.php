<?php

$factory->define(App\Participant::class, function (Faker\Generator $faker) {
    return [
        'draw_id'   => factory(App\Draw::class),
        'name'      => $faker->name,
        'address'   => $faker->email,
        'target_id' => null,
        'mail_id'   => factory(App\Mail::class),
    ];
});
