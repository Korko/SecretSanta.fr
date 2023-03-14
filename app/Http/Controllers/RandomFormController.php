<?php

namespace App\Http\Controllers;

use App\Exceptions\SolverException;
use App\Http\Requests\RandomFormRequest;
use App\Jobs\ProcessPendingDraw;
use App\Models\PendingDraw;
use Arr;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RandomFormController extends Controller
{
    public function index(): Response
    {
        return response()->inertia('RandomForm', [
            'bmc' => config('app.bmc'),
        ]);
    }

    public function handle(RandomFormRequest $request): JsonResponse
    {
        $safe = $request->safe();

        if (! Arr::get($safe, 'participant-organizer', false)) {
            $organizer = $safe['organizer'];
        } else {
            $organizer = current($safe['participants']);
            unset($organizer['exclusions']);
        }

        $pending = new PendingDraw;
        $pending->organizer_name = $organizer['name'];
        $pending->organizer_email = $organizer['email'];
        $pending->data = $safe->toArray();
        $pending->save();

        $pending->markAsReady();

        return response()->jsonTry(
            closure: function () use ($pending) {
                ProcessPendingDraw::dispatchSync($pending);

                return [
                    'draw' => $pending->fresh()->draw->id,
                ];
            },
            onSuccess: trans('error.solution'),
            onFailure: trans('message.sent'),
            exceptionClass: SolverException::class,
            errorCode: 422
        );
    }
}
