<?php

namespace App\Http\Controllers;

use App\Models\PendingDraw;
use App\Notifications\PendingDraw as PendingDrawNotification;
use App\Http\Requests\RandomFormRequest;
use Arr;
use Lang;
use Notification;

class RandomFormController extends Controller
{
    public function handle(RandomFormRequest $request)
    {
        $safe = $request->safe();

        if(!Arr::get($safe, 'participant-organizer', false)) {
            $organizer = $safe['organizer'];
        } else {
            $organizer = current($safe['participants']);
            unset($organizer['exclusions']);
        }

        $draw = new PendingDraw;
        $draw->organizer_name = $organizer['name'];
        $draw->organizer_email = $organizer['email'];
        $draw->data = $safe->toArray();
        $draw->save();

        Notification::route('mail', [$organizer])->notify(new PendingDrawNotification($draw));

        return response()->json([
            'message' => trans('message.pending')
        ]);
    }
}
