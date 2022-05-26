<?php

namespace App\Http\Controllers;

use URL;

class ErrorController extends Controller
{
    public static function pageNotFound()
    {
        return static::renderWithInertia('PageNotFound.vue', [
            'routes' => [
                'form' => URL::route('form.index'),
                'dashboard' => URL::route('dashboard'),
            ],
        ]);
    }

    public static function drawNotFound()
    {
        return static::renderWithInertia('DrawNotFound.vue', [
            'routes' => [
                'form' => URL::route('form.index'),
                'dashboard' => URL::route('dashboard'),
            ],
        ]);
    }
}
