<?php

namespace App\Http\Controllers;

use App\Facades\DrawCrypt;
use App\Notifications\OrganizerRecap;
use App\Notifications\TargetDrawn;
use Arr;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use URL;
use URLParser;

class FixOrganizerController extends Controller
{
    public function view(): Response
    {
        return response()->view('fixOrganizer', [
            'fixUrl' => URL::route('fixOrganizer.handle'),
        ]);
    }

    public function handle(Request $request): JsonResponse
    {
        $hash = Arr::get(explode('#', $request->input('url'), 2), 1);
        $key = base64_decode($hash);
        DrawCrypt::setIV($key);

        $participant = URLParser::parseByName('dearSanta', $request->input('url'))->participant;
        if (! isset($participant)) {
            return response()->json([
                'errors' => [
                    'url' => [
                        trans('error.fixOrganizer.drawNotFoundOrExpired'),
                    ],
                ],
            ], 422);
        }

        try {
            $draw = $participant->draw;

            // Access the email in a try/catch to handle invalid decrypt key
            $organizerEmail = $draw->organizer->email;
        } catch (\Exception $e) {
            return response()->json([
                'errors' => [
                    'url' => [
                        trans('error.fixOrganizer.drawNotFoundOrExpired'),
                    ],
                ],
            ], 422);
        }

        if (levenshtein($request->input('email'), $organizerEmail) > 3) {
            return response()->json([
                'errors' => [
                    'email' => [
                        trans('error.fixOrganizer.distanceEmailTooBig'),
                    ],
                ],
            ], 422);
        }

        if ($draw->organizer->mail->updated_at->diffInSeconds(Carbon::now()) <= config('mail.resend_delay')) {
            return response()->json([
                'errors' => [
                    'email' => [
                        trans('error.fixOrganizer.resendDelayTooShort'),
                    ],
                ],
            ], 422);
        }

        $draw->organizer->email = $request->input('email');
        $draw->organizer->save();

        $draw->organizer->notify(new OrganizerRecap($draw));
        if($draw->participant_organizer) {
            $draw->organizer->notifyNow(new TargetDrawn);
        }

        return response()->json([
            'message' => trans('message.fixedOrganizer'),
        ]);
    }
}
