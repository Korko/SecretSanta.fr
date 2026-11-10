<?php

declare(strict_types=1);

use App\Http\Middleware\HandleEncryptionIV;
use App\Http\Middleware\Language;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Korko\Validator\ValidatorServiceProvider;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        ValidatorServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        // api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // $middleware->throttleApi();

        $middleware->alias([
            'decrypt.iv' => HandleEncryptionIV::class,
        ]);

        $middleware->web(remove: [
            EncryptCookies::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            PreventRequestForgery::class,
        ]);
        // Locale from the domain (APP_DOMAIN_*), APP_LOCALE otherwise
        $middleware->web(append: [Language::class]);

        $middleware->priority([
            SubstituteBindings::class,
            HandleEncryptionIV::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Only the field names: the values are names, emails and messages kept encrypted everywhere else
        $exceptions->context(fn (): array => [
            'url' => Request::url(),
            'input_keys' => array_keys(Request::all()),
        ]);

        $exceptions->render(fn (ValidationException $e, $request) => response()->json([
            'message' => trans('error.validation'),
            'errors' => $e->errors(),
        ], 422));

        $exceptions->render(fn (InvalidSignatureException $e, $request) => response()->json([
            'message' => trans('error.signature'),
        ], 500));
    })->create();
