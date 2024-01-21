<?php

use App\Enums\AppMode;

// Cannot use BackedEnum as key so cast to get the actual value (AppMode::MODE)->value
return [
    'limitations' => [
        'participants' => [
            (AppMode::FREE)->value => 20,
            (AppMode::OPEN)->value => 100,
        ],
    ],
];
