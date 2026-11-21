<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\FixOrganizerRequest;
use App\Notifications\OrganizerEmailChanged;
use App\Notifications\OrganizerRecap;
use App\Notifications\TargetDrawn;
use App\Services\URLParser;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

final class FixOrganizerController extends Controller
{
    public const int LOCK_MINUTES = 10;

    public function view(): Response
    {
        return response()->view('fixOrganizer', [
            'fixUrl' => URL::route('fixOrganizer.handle'),
        ]);
    }

    public function handle(FixOrganizerRequest $request): JsonResponse
    {
        $url = $request->string('url')->toString();
        $parser = resolve(URLParser::class);
        $notFound = ValidationException::withMessages(['url' => trans('error.fixOrganizer.drawNotFoundOrExpired')]);

        // A link without its secret (after #), unknown, removed, or of a draw that ended
        throw_unless($parser->setIVFromUrl($url), $notFound);
        $draw = $parser->participant($url)?->draw ?? throw $notFound;

        try {
            // A wrong secret in the link can't decrypt the draw
            $organizerEmail = $draw->organizer_email;
            // @phpstan-ignore catch.neverThrown (reading an encrypted attribute decrypts it)
        } catch (DecryptException) {
            throw $notFound;
        }

        // Right after the draw, the organizer hasn't had time to open their email yet: a participant could redirect
        // it before anything proves it works. The message gives no unlock time on purpose
        throw_if($draw->created_at?->isAfter(Date::now()->subMinutes(self::LOCK_MINUTES)) ?? false, ValidationException::withMessages([
            'email' => trans('error.fixOrganizer.tooEarly', ['minutes' => self::LOCK_MINUTES]),
        ]));

        $email = $request->string('email')->toString();
        // The address already given: nothing to fix, the organizer only asks for their emails again
        $sameEmail = $email === $organizerEmail;

        // Only for an address that never worked: otherwise anyone with a dearSanta link could take the panel over
        throw_if(! $sameEmail && $draw->organizerReceived(), ValidationException::withMessages(['email' => trans('error.fixOrganizer.alreadyReceived')]));

        throw_if(levenshtein($email, $organizerEmail) > 3, ValidationException::withMessages(['email' => trans('error.fixOrganizer.distanceEmailTooBig')]));

        // The organizer's own email: their target email when taking part, else the recap
        $organizerMail = $draw->organizer->mail ?? $draw->recapMail();
        throw_if(($organizerMail->updated_at?->diffInSeconds(Date::now()) ?? PHP_INT_MAX) <= config('mail.resend_delay'), ValidationException::withMessages([
            'email' => trans('error.fixOrganizer.resendDelayTooShort'),
        ]));

        // All or nothing: the emails only go once everything is saved (queued after the commit)
        DB::transaction(function () use ($draw, $organizerEmail, $email, $sameEmail): void {
            $organizer = $draw->organizer;

            if ($sameEmail) {
                // Sending again resets the delivery statuses: keep the proof of reception, it locks /fix
                if ($draw->organizer_received_at === null && $draw->organizerReceived()) {
                    $draw->organizer_received_at = Date::now();
                    $draw->save();
                }
            } else {
                Notification::route('mail', [$organizerEmail => $draw->organizer_name])
                    ->notify(new OrganizerEmailChanged($draw, $email));

                $draw->organizer_email = $email;
                $draw->save();

                if ($organizer !== null) {
                    $organizer->email = $email;
                    $organizer->save();
                }
            }

            $draw->recapMail()->markAsCreated();
            $draw->notify(new OrganizerRecap($draw));

            $organizer?->notify(new TargetDrawn);
        });

        return response()->json([
            'message' => trans($sameEmail ? 'message.resentOrganizer' : 'message.fixedOrganizer'),
        ]);
    }
}
