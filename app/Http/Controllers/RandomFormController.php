<?php

namespace App\Http\Controllers;

use App\Enums\PendingParticipantStatus;
use App\Exceptions\SolverException;
use App\Http\Requests\RandomFormRequest;
use App\Jobs\ProcessPendingDraw;
use App\Models\PendingDraw;
use App\Models\PendingParticipant;
use App\Notifications\PendingDrawConfirm;
use Arr;
use Illuminate\Http\JsonResponse;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
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

        $pending = new PendingDraw;
        $pending->title = $safe['title'];
        $pending->organizer_name = $safe['organizer-name'];
        $pending->organizer_email = $safe['organizer-email'];
        $pending->save();

        if($safe['participant-organizer'] ?? false) {
            $organizer = $pending->participants()->create([
                // Don't use $pending->organizer_* here, as they are encrypted
                'name' => $safe['organizer-name'],
                'email' => $safe['organizer-email'],
            ]);

            $pending->organizer()->associate($organizer);
        }

        foreach(($safe->participants ?? []) as $participant) {
            $pending->participants()->create([
                'name' => $participant,
            ]);
        }

        $pending->organizer->notify(new PendingDrawConfirm($pending));

        $link = route('pending.join', ['pendingDraw' => $pending->hash]);
        return response()->json([
            'link' => $link,
            'qrcode' => QrCode::format('png')
                ->size(256)
                ->generate($link),
            'message' => trans('message.pending'),
        ]);
    }
}
