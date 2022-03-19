<?php

namespace App\Http\Controllers;

use App\Exceptions\SolverException;
use App\Models\PendingDraw;
use App\Notifications\OrganizerRecap;
use App\Services\DrawFormHandler;
use Lang;
use Notification;
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
                'process' => URL::signedRoute('pending.process', ['pending' => $pending])
            ],
            'pending' => $pending->only(['id', 'status', 'updated_at', 'organizer_name']),
        ]);
    }

    /**
     * Return encrypted data
     */
    public function fetch(PendingDraw $pending)
    {
        return response()->json([
            'pending' => $pending->only(['data'])
        ]);
    }

    public function process(PendingDraw $pending)
    {
        // Maybe not the best system, can happen very often
        abort_if(! $pending->isWaiting(), 422, Lang::get('error.processing'));

        try {
            $pending->markAsDrawing();

            $draw = (new DrawFormHandler)->handle($pending);

            Notification::route('mail', [
                ['name' => $draw->organizer_name, 'email' => $draw->organizer_email],
            ])->notify(new OrganizerRecap($draw));

            $draw->createMetric('new_draw')
                ->addExtra('participants', count($draw->participants));

            $pending->markAsStarted($draw);

            return response()->json([
                'message' => trans('message.sent')
            ]);
        } catch(SolverException $e) {
            $pending->markAsWaiting();

            return response()->json([
                'message' => trans('error.solution'),
            ], 422);
        }
    }
}