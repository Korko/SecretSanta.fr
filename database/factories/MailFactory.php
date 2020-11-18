<?php

$factory->define(App\Models\Mail::class, function (Faker\Generator $faker) {
    return [
        'delivery_status' => App\Models\Mail::CREATED,
        'draw_id' => factory(App\Models\Draw::class),
        'version' => $faker->numberBetween(0, 255),
    ];
});
