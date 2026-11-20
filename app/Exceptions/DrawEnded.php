<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Lang;
use RuntimeException;

// An action on an ended draw: expired: true lets the page switch to "over" without a reload,
// whatever the browser clock says
final class DrawEnded extends RuntimeException
{
    public function render(): JsonResponse
    {
        return response()->json([
            'message' => Lang::get('error.ended'),
            'expired' => true,
        ], 403);
    }
}
