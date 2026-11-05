<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\HandleEncryptionIV;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        \Korko\Validator\ValidatorServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        // api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // $middleware->throttleApi();

        $middleware->alias([
            'decrypt.iv' => HandleEncryptionIV::class,
        ]);

        $middleware->web(remove: [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class,
        ]);

        $middleware->priority([
            SubstituteBindings::class,
            HandleEncryptionIV::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->context(fn () => [
            'url' => Request::url(),
            'input' => Request::all(),
        ]);

        $exceptions->render(function (ValidationException $e, $request) {
            return response()->json([
                'message' => trans('error.validation'),
                'errors' => $e->errors(),
            ], 422);
        });

        $exceptions->render(function (InvalidSignatureException $e, $request) {
            return response()->json([
                'message' => trans('error.signature'),
            ], 500);
        });
    })->create();
