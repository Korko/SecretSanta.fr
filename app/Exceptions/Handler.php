<?php

namespace App\Exceptions;

use App\Exceptions\SolverException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ValidationException) {
            return response()->json([
                'message' => trans('error.validation'),
            ], 500);
        }

        if ($exception instanceof SolverException) {
            return response()->json([
                'message' => trans('error.solution')
            ], 500);
        }

        if ($exception instanceof InvalidSignatureException) {
            return response()->json([
                'message' => trans('error.signature'),
            ], 500);
        }

        return parent::render($request, $exception);
    }
}
