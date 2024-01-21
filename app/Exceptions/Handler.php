<?php

namespace App\Exceptions;

use App\Http\Controllers\ErrorController;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of exceptions with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var string[]
     */
    protected $dontReport = [
        \Illuminate\Broadcasting\BroadcastException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var string[]
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (ValidationException $e, Request $request) {
            return ErrorController::validationError($e);
        });

        $this->renderable(function (InvalidSignatureException $e, Request $request) {
            return ErrorController::invalidSignature();
        });

        $this->renderable(function (NotFoundHttpException $e, Request $request) {
            return ErrorController::pageNotFound();
        });

        $this->renderable(function (ModelNotFoundException $e, Request $request) {
            return ErrorController::drawNotFound();
        });
    }

    /**
     * Get the default context variables for logging.
     *
     * @return array
     */
    protected function context()
    {
        return array_merge(parent::context(), [
            'url' => request()->url(),
            'input' => request()->all(),
        ]);
    }
}
