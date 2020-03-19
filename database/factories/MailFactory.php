<?php

$factory->define(App\Mail::class, function (Faker\Generator $faker) {
    return [
        'delivery_status' => App\Mail::CREATED,
    ];
});
