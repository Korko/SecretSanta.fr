<?php

$factory->define(App\Models\Mail::class, function (Faker\Generator $faker) {
    return [
        'delivery_status' => App\Models\Mail::CREATED,
    ];
});
