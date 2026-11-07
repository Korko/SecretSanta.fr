<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Facades\DrawCrypt;
use App\Http\Requests\FixOrganizerRequest;
use App\Models\Draw;
use App\Notifications\OrganizerEmailChanged;
use App\Notifications\OrganizerRecap;
use App\Notifications\TargetDrawn;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use URLParser;

final class FixOrganizerController extends Controller
{
    public function view(): Response
    {
        return response()->view('fixOrganizer', [
            'fixUrl' => URL::route('fixOrganizer.handle'),
        ]);
    }

    public function handle(FixOrganizerRequest $request): JsonResponse
    {
        $hash = Arr::get(explode('#', (string) $request->input('url'), 2), 1, '');
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
            /** @var Draw $draw */
            $draw = $participant->draw;

            // Access the email in a try/catch to handle invalid decrypt key
            $organizerEmail = $draw->organizer_email;
        } catch (Exception) {
            return response()->json([
                'errors' => [
                    'url' => [
                        trans('error.fixOrganizer.drawNotFoundOrExpired'),
                    ],
                ],
            ], 422);
        }

        // Only for an address that never worked: otherwise anyone with a dearSanta link could take the panel over
        if ($draw->organizerReceived()) {
            return response()->json([
                'errors' => [
                    'email' => [
                        trans('error.fixOrganizer.alreadyReceived'),
                    ],
                ],
            ], 422);
        }

        $email = $request->string('email')->toString();
        if (levenshtein($email, $organizerEmail) > 3) {
            return response()->json([
                'errors' => [
                    'email' => [
                        trans('error.fixOrganizer.distanceEmailTooBig'),
                    ],
                ],
            ], 422);
        }

        if (($draw->organizer->mail->updated_at?->diffInSeconds(Date::now()) ?? PHP_INT_MAX) <= config('mail.resend_delay')) {
            return response()->json([
                'errors' => [
                    'email' => [
                        trans('error.fixOrganizer.resendDelayTooShort'),
                    ],
                ],
            ], 422);
        }

        $participantOrganizer = false;
        if ($draw->organizer->email === $draw->organizer_email) {
            $participantOrganizer = true;
        }

        Notification::route('mail', [$organizerEmail => $draw->organizer_name])
            ->notify(new OrganizerEmailChanged($draw, $email));

        $draw->organizer_email = $email;
        $draw->save();

        Notification::route('mail', [
            $draw->organizer_email => $draw->organizer_name,
        ])->notify(new OrganizerRecap($draw));

        if ($participantOrganizer) {
            $draw->organizer->email = $email;
            $draw->organizer->save();

            $draw->organizer->notifyNow(new TargetDrawn);
        }

        return response()->json([
            'message' => trans('message.fixedOrganizer'),
        ]);
    }
}
