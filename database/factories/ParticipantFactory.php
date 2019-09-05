<?php

$factory->define(App\Participant::class, function (Faker\Generator $faker) {
    return [
        'draw_id'         => $faker->numberBetween(1),
        'name'            => $faker->name,
        'email_address'   => $faker->email,
        'email_id'        => null,
        'delivery_status' => App\Participant::CREATED,
        'target'          => [
            'name'        => $faker->name,
        ],
    ];
});
