<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;

class ErrorController extends Controller
{
    public static function pageNotFound(): Response
    {
        return response()->inertia('PageNotFound');
    }

    public static function drawNotFound(): Response
    {
        return response()->inertia('DrawNotFound');
    }
}
