<?php

$factory->define(App\Models\Participant::class, function (Faker\Generator $faker) {
    return [
        'draw_id'   => factory(App\Models\Draw::class),
        'name'      => $faker->name,
        'email'     => $faker->email,
        'target_id' => null,
        'mail_id'   => factory(App\Models\Mail::class),
    ];
});
