<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;

class ErrorController extends Controller
{
    public static function pageNotFound(): Response
    {
        return response()
            ->inertia('PageNotFound')
            ->setStatusCode(404);
    }

    public static function drawNotFound(): Response
    {
        return response()
            ->inertia('DrawNotFound')
            ->setStatusCode(404);
    }
}
