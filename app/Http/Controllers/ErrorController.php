<?php

namespace App\Http\Controllers;

use URL;

class ErrorController extends Controller
{
    public static function pageNotFound()
    {
        return response()->inertia('PageNotFound.vue', [
            'routes' => [
                'form' => URL::route('form.index'),
                'dashboard' => URL::route('dashboard'),
            ],
        ]);
    }

    public static function drawNotFound()
    {
        return response()->inertia('DrawNotFound.vue', [
            'routes' => [
                'form' => URL::route('form.index'),
                'dashboard' => URL::route('dashboard'),
            ],
        ]);
    }
}
