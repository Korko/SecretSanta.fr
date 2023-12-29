<?php

namespace App\Http\Controllers;

use App\Http\Requests\RandomFormRequest;
use App\Models\Draw;
use App\Notifications\PendingDrawConfirm;
use Illuminate\Http\JsonResponse;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Symfony\Component\HttpFoundation\Response;

class RandomFormController extends Controller
{
    public function display(): Response
    {
        return response()->view('pages/random-form');
    }

    public function handle(RandomFormRequest $request): JsonResponse
    {
        $safe = $request->safe();

        $draw = new Draw;
        $draw->title = $safe['title'];
        $draw->description = $safe['description'] ?? null;
        $draw->budget = $safe['budget'];
        $draw->event_date = $safe['event-date'] ?? null;
        $draw->participant_organizer = $safe['participant-organizer'] ?? false;
        $draw->save();

        $organizer = $draw->participants()->create([
            'name' => $safe['organizer']['name'],
            'email' => $safe['organizer']['email'],
        ]);
        $draw->organizer()->associate($organizer);
        $draw->save();

        foreach(($safe->participants ?? []) as $participant) {
            $draw->participants()->create([
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
            'draw' => $draw->uid
        ]);
    }
}
