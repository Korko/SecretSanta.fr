<?php

namespace App\Http\Controllers;

use App\Http\Requests\RandomFormRequest;
use App\Models\Draw;
use App\Notifications\PendingDrawConfirm;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Symfony\Component\HttpFoundation\Response;

class RandomFormController extends Controller
{
    public function display(): Response
    {
        return response()->inertia('RandomForm', [
            'bmc' => config('app.bmc'),
        ]);
    }

    public function handle(RandomFormRequest $request): JsonResponse
    {
        $safe = $request->safe();

        $draw = new Draw;
        $draw->title = $safe['title'];
        $draw->description = $safe['description'] ?? null;
        $draw->budget = $safe['budget'];
        $draw->event_date = $safe['event-date'] ?? null;
        $draw->organizer_name = $safe['organizer-name'];
        $draw->organizer_email = $safe['organizer-email'];
        $draw->save();

        if($safe['participant-organizer'] ?? false) {
            $organizer = $draw->participants()->create([
                'ulid' => Str::ulid(),
                // Don't use $draw->organizer_* here, as they are encrypted
                'name' => $safe['organizer-name'],
                'email' => $safe['organizer-email'],
            ]);

            $draw->organizer()->associate($organizer);
        }

        foreach(($safe->participants ?? []) as $participant) {
            $draw->participants()->create([
                'ulid' => Str::ulid(),
                'name' => $participant['name'],
                'email' => $participant['email'] ?? null,
            ]);
        }

        $draw->organizer->notify(new PendingDrawConfirm($draw));

        $link = route('pending.join', ['draw' => $draw]);
        return response()->json([
            'link' => $link,
            'qrcode' => QrCode::format('png')
                ->size(256)
                ->generate($link),
            'message' => trans('message.pending'),
        ]);
    }
}
