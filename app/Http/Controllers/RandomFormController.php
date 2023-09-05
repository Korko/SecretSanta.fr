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
    public function index(): Response
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
        $draw->organizer_name = $safe['organizer-name'];
        $draw->organizer_email = $safe['organizer-email'];
        $draw->save();

        if($safe['participant-organizer'] ?? false) {
            $organizer = $draw->participants()->create([
                // Don't use $draw->organizer_* here, as they are encrypted
                'name' => $safe['organizer-name'],
                'email' => $safe['organizer-email'],
            ]);

            $draw->organizer()->associate($organizer);
        }

        foreach(($safe->participants ?? []) as $participant) {
            $draw->participants()->create([
                'name' => $participant,
            ]);
        }

        $draw->organizer->notify(new PendingDrawConfirm($draw));

        $link = route('pending.join', ['pending_draw' => $draw->hash]);
        return response()->json([
            'link' => $link,
            'qrcode' => QrCode::format('png')
                ->size(256)
                ->generate($link),
            'message' => trans('message.pending'),
        ]);
    }
}
