<?php

namespace App\Http\Controllers;

use Illuminate\Validation\ValidationException;
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

    public static function invalidSignature(): Response
    {
        return response()->json([
            'message' => trans('error.signature'),
        ], 500);
    }

    public static function validationError(ValidationException $e): Response
    {
        return response()->json([
            'message' => trans('error.validation'),
            'errors' => $e->errors(),
        ], 422);
    }
}
