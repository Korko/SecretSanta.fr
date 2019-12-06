<?php

$factory->define(App\Participant::class, function (Faker\Generator $faker) {
    return [
        'draw_id'         => function () {
            return factory(App\Draw::class)->create()->id;
        },
        'name'            => $faker->name,
        'email_address'   => $faker->email,
        'email_id'        => null,
        'delivery_status' => App\Participant::CREATED,
        'target_id'       => null
    ];
});
