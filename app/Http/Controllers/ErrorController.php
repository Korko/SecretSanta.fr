<?php

namespace App\Http\Controllers;

class ErrorController extends Controller
{
    public static function pageNotFound()
    {
        return response()->inertia('PageNotFound');
    }

    public static function drawNotFound()
    {
        return response()->inertia('DrawNotFound');
    }
}
