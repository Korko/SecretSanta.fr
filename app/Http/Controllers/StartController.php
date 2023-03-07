<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessPendingDraw;
use App\Models\PendingDraw;
use Illuminate\Http\JsonResponse;
use Lang;
use URL;

class StartController extends Controller
{
    public function index(PendingDraw $pending)
    {
        return static::renderWithInertia('PendingDraw.vue', [
            'routes' => [
                'form' => URL::route('form.index'),
                'dashboard' => URL::route('dashboard'),
                'fetch' => URL::signedRoute('pending.fetch', ['pending' => $pending]),
                'process' => URL::signedRoute('pending.process', ['pending' => $pending]),
            ],
            'pending' => $pending->only(['id', 'status', 'updated_at', 'organizer_name']),
        ]);
    }

    /**
     * Return encrypted data
     */
    public function fetch(PendingDraw $pending): JsonResponse
    {
        return response()->json([
            'pending' => $pending->only(['data']),
        ]);
    }

    public function process(PendingDraw $pending)
    {
        // Maybe not the best system, can happen very often
        abort_if(! $pending->isWaiting(), 422, Lang::get('error.processing'));

        // Mark ready to be processed
        $pending->markAsReady();

        dispatch(new ProcessPendingDraw($pending));
    }
}
