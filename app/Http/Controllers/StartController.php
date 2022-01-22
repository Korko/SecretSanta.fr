<?php

namespace App\Http\Controllers;

use App\Exceptions\SolverException;
use App\Models\PendingDraw;
use App\Notifications\OrganizerRecap;
use App\Services\DrawFormHandler;
use Lang;
use Notification;

class StartController extends Controller
{
    public function index(PendingDraw $pending)
    {
        return view('pending', [
            'draw' => $pending->only(['id', 'status', 'updated_at']),
        ]);
    }

    public function process(PendingDraw $pending)
    {
        abort_if(! $pending->isWaiting(), 422, Lang::get('error.processing'));// Maybe not the best system, can happen very often

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