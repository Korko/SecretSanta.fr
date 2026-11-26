<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\DeliveryStatus;
use App\Enums\ReportReason;
use App\Events\ParticipantUpdated;
use App\Http\Requests\DearSantaRequest;
use App\Models\DearSanta;
use App\Models\GiftSuggestion;
use App\Models\Mail;
use App\Models\Participant;
use App\Notifications\DearSanta as DearSantaNotification;
use App\Notifications\GiftSuggestion as GiftSuggestionNotification;
use App\Notifications\OrganizerRecap;
use App\Notifications\ParticipantEmailCode;
use App\Notifications\ParticipantReported;
use App\Notifications\ParticipantWithdrew;
use App\Notifications\TargetDrawn;
use App\Services\ParticipantsEditor;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

final class DearSantaController extends Controller
{
    // What the participant page calls, also on the organizer's panel for their own participant part
    /** @return array<string, string> */
    public static function routes(Participant $participant): array
    {
        $routes = collect([
            'fetchUrl' => 'dearSanta.fetch',
            'contactUrl' => 'dearSanta.contact',
            'fetchStateUrl' => 'dearSanta.fetchState',
            'reportUrl' => 'dearSanta.report',
            'requestEmailChangeUrl' => 'dearSanta.request_email_change',
            'changeEmailUrl' => 'dearSanta.change_email',
            'withdrawUrl' => 'dearSanta.withdraw',
            'suggestUrl' => 'dearSanta.suggest',
        ])->map(fn (string $route): string => URL::signedRoute($route, ['participant' => $participant]));

        return ['hash' => $participant->hash, ...$routes->all()];
    }

    public function view(Participant $participant): Response|RedirectResponse
    {
        // Taking part, the organizer has a single page: their panel, with their participant part in it.
        // Their earlier emails link here: the browser keeps the #secret through the redirect
        if ($participant->isOrganizer()) {
            return redirect()->to(URL::signedRoute('organizerPanel', ['draw' => $participant->draw->hash]));
        }

        return response()->view('dearSanta', [
            'routes' => self::routes($participant),
        ]);
    }

    public function fetch(Participant $participant): JsonResponse
    {
        // The hash was validated in middleware so we can validate that the email was received
        $participant->mail->markAsReceived();

        // Taking part, the organizer got the panel link in this same email
        if ($participant->draw->organizer_received_at === null && $participant->isOrganizer()) {
            $participant->draw->organizer_received_at = now();
            $participant->draw->save();
        }

        return response()->json([
            'participant' => $participant->only(['hash', 'name']),
            // Already in their draw email: the page shows it too (they can hide it on their device)
            'target' => $participant->target->name,
            'title' => $participant->draw->title($participant),
            // "Le jour J": the date, then the place and the budget when given
            'event' => [
                'expires_at' => $participant->draw->expires_at->toDateString(), // event day, whatever the browser timezone
                'location' => $participant->draw->location,
                'budget' => OrganizerRecap::eventDetails($participant->draw)['budget'],
            ],
            'targetDearSantaLastUpdate' => $participant->target->dearSantas->load('mail')->max('mail.updated_at'),
            'channel' => $participant->broadcastChannel(),
            'organizer' => $participant->draw->organizer_name,
            'targetsRevealed' => $participant->draw->targets_revealed_at !== null,
            'ends_at' => $participant->draw->ends_at, // after it, the organizer sees every target
            'report' => $participant->report,
            'isOrganizer' => $participant->isOrganizer(),
            'email' => $participant->email,
            'suggestionRecipients' => $this->suggestionRecipients($participant)->mapWithKeys(fn (Participant $recipient): array => [$recipient->hash => $recipient->name]),
            'suggestions' => $this->sentSuggestions($participant),
            'receivedSuggestions' => $this->receivedSuggestions($participant),
            'pendingEmail' => $this->pendingEmail($participant),
            'emails' => $participant->dearSantas->mapWithKeys(fn ($email): array => [
                $email->mail->id => $this->publicDearSanta($email),
            ]),
            'resendTargetEmailsUrl' => URL::signedRoute('dearSanta.resend_target', [
                'participant' => $participant,
            ]),
        ]);
    }

    // "Not me" / "not taking part": emailed to the organizer and shown on their panel, null withdraws the report
    public function report(Request $request, Participant $participant): JsonResponse
    {
        // The organizer has their panel (and can delete the draw), reporting to themselves makes no sense
        abort_if($participant->isOrganizer(), 403);

        $request->validate(['reason' => ['nullable', Rule::enum(ReportReason::class)]]);

        $previous = $participant->report;
        $participant->report = $request->enum('reason', ReportReason::class);
        $participant->save();
        $participant->draw->refuseEmail($participant->email, $participant->report !== null);

        $participant->createMetric($participant->report ? 'report_'.$participant->report->value : 'report_cancelled');

        // A mistyped address is best fixed before the right person thinks they were forgotten: the organizer is
        // told right away, once per reason (not on a cancellation, nor again for the same one)
        if ($participant->report !== null && $participant->report !== $previous) {
            $participant->draw->notify(new ParticipantReported($participant->name, $participant->email, $participant->report));
        }

        return response()->json(['report' => $participant->report]);
    }

    // A gift idea for someone else than their own target, emailed to that person's santa (whom the sender doesn't know)
    public function suggest(Request $request, Participant $participant): JsonResponse
    {
        $recipients = $this->suggestionRecipients($participant);
        $request->validate([
            'recipient' => ['required', Rule::in($recipients->pluck('hash')->all())],
            'content' => ['required', 'string', 'max:2000'],
            'signed' => ['sometimes', 'boolean'],
        ], [
            'recipient.required' => __('validation.custom.dearSanta.recipient.required'),
            'recipient.in' => __('validation.custom.dearSanta.recipient.required'),
            'content.required' => __('validation.custom.dearSanta.content.required'),
        ]);

        /** @var Participant $recipient validated above */
        $recipient = $recipients->firstWhere('hash', $request->string('recipient')->toString());

        $suggestion = new GiftSuggestion([
            'mail_body' => $request->string('content')->toString(),
            'signed' => $request->boolean('signed'),
        ]);
        $suggestion->sender()->associate($participant);
        $suggestion->recipient()->associate($recipient);
        $suggestion->save();

        $participant->createMetric('gift_suggestion');

        $recipient->santa->notify(new GiftSuggestionNotification($suggestion));
        event(new ParticipantUpdated($recipient->santa));
        event(new ParticipantUpdated($participant));

        return response()->json([
            'message' => trans('message.sent'),
            'suggestion' => $this->publicSuggestion($suggestion->load('mail', 'recipient')),
        ]);
    }

    // "I signed up but changed my mind": unlike a report, they really leave. Their santa gets a new target
    // and the organizer an email. This page and its link stop working, but the organizer can bring them back:
    // someone else may have got their email and withdrawn in their place (see ParticipantsEditor::withdraw)
    public function withdraw(Participant $participant): JsonResponse
    {
        // The organizer has their panel to remove themselves, or to delete the draw
        abort_if($participant->isOrganizer(), 403);

        $draw = $participant->draw;
        $name = $participant->name;
        try {
            new ParticipantsEditor($draw)->withdraw($participant);
        } catch (ValidationException $validationException) {
            // Only the organizer may break an exclusion to let them go
            throw array_key_exists('exclusions', $validationException->errors())
                ? ValidationException::withMessages(['exclusions' => trans('dearsanta.withdraw.blocked', ['organizer' => $draw->organizer_name])])
                : $validationException;
        }

        // Counted once it went through
        $participant->createMetric('withdraw')->addTag('by', 'participant');

        $draw->notify(new ParticipantWithdrew($name));

        return response()->json([
            'message' => trans('dearsanta.withdraw.done'),
        ]);
    }

    // Another mailbox: a code is sent there first, the address only changes once it is typed back (changeEmail)
    public function requestEmailChange(Request $request, Participant $participant): JsonResponse
    {
        $request->validate(['email' => ['required', 'email']], [
            'email.required' => __('validation.custom.dearSanta.email.required'),
            'email.email' => __('validation.custom.dearSanta.email.format'),
        ]);
        $email = $request->string('email')->trim()->toString();

        throw_if(mb_strtolower($email) === mb_strtolower($participant->email), ValidationException::withMessages(['email' => trans('dearsanta.email.same')]));
        throw_if($participant->draw->refusesEmail($email), ValidationException::withMessages(['email' => trans('organizer.report.refused_email')]));
        // A typo can be fixed right away, but not one email a second to any address
        abort_if($participant->pending_email_at !== null && $participant->pending_email_at->diffInSeconds(Date::now()) < config()->integer('mail.email_change.request_delay'), 403, Lang::get('error.resend'));

        $code = $participant->requestEmailChange($email);
        Notification::route('mail', [$email => $participant->name])->notify(new ParticipantEmailCode($participant->name, $code));

        $participant->createMetric('email_change_requested');

        return response()->json([
            'message' => trans('dearsanta.email.code_sent', ['email' => $email]),
            'pendingEmail' => $email,
        ]);
    }

    public function changeEmail(Request $request, Participant $participant): JsonResponse
    {
        $request->validate(['code' => ['required', 'string']], [
            'code.required' => __('validation.custom.dearSanta.code.required'),
        ]);

        $email = $this->pendingEmail($participant);
        throw_if($email === null, ValidationException::withMessages(['code' => trans('dearsanta.email.expired')]));

        // Counted before checking, in a single query: parallel requests can't try more codes than allowed
        $counted = Participant::query()->whereKey($participant->id)
            ->where('pending_email_attempts', '<', config()->integer('mail.email_change.code_attempts'))
            ->increment('pending_email_attempts');
        throw_if($counted === 0, ValidationException::withMessages(['code' => trans('dearsanta.email.expired')]));

        if (! $participant->checkEmailCode(preg_replace('/\D/', '', $request->string('code')->toString()) ?? '')) {
            // Six digits: a few tries for typos, then a new code is needed
            $last = $participant->pending_email_attempts + 1 >= config()->integer('mail.email_change.code_attempts');
            if ($last) {
                $participant->clearPendingEmail();
                $participant->save();
            }

            throw ValidationException::withMessages(['code' => trans($last ? 'dearsanta.email.expired' : 'dearsanta.email.wrong_code')]);
        }

        // Refused in the meantime ("not me" from another participant using it)
        throw_if($participant->draw->refusesEmail($email), ValidationException::withMessages(['code' => trans('organizer.report.refused_email')]));

        // The organizer moving their own address: keep the draw in sync (recap in their target email, /fix)
        if ($participant->isOrganizer()) {
            $participant->draw->organizer_email = $email;
            $participant->draw->save();
        }

        $participant->email = $email;
        $participant->report = null;
        $participant->clearPendingEmail();
        $participant->save();

        $participant->createMetric('change_email')->addTag('by', 'participant');

        // Their target email again, at the new address: the organizer panel tracks it like any resend.
        // Nor did the new address get what their target wrote or was given: it comes in that same email
        $participant->mail->markAsCreated();
        $participant->notify(new TargetDrawn);

        return response()->json([
            'message' => trans('dearsanta.email.changed', ['email' => $email]),
            'email' => $email,
        ]);
    }

    // What the page refetches on a participant.update event (ParticipantUpdated)
    public function fetchState(Participant $participant): JsonResponse
    {
        return response()->json([
            'suggestions' => $this->sentSuggestions($participant),
            'receivedSuggestions' => $this->receivedSuggestions($participant),
            'emails' => $participant->dearSantas->mapWithKeys(fn ($dearSanta): array => [
                $dearSanta->mail->id => $this->publicDearSanta($dearSanta),
            ]),
        ]);
    }

    // Their recap again, in a single email: the messages their target wrote to them and the gift ideas given for
    // that target. Nothing changes on their senders' pages
    public function resendTarget(Participant $participant): JsonResponse
    {
        $sent = RateLimiter::attempt('resend-target:'.$participant->id, 1, $participant->forwardTargetMessages(...), config()->integer('mail.resend_delay'));
        abort_if($sent === false, 403, Lang::get('error.resend'));

        $participant->createMetric('resend_target_messages');

        return response()->json([
            'message' => trans('message.sent'),
        ]);
    }

    public function handle(Participant $participant, DearSantaRequest $request): JsonResponse
    {
        $dearSanta = new DearSanta;
        $dearSanta->sender()->associate($participant);
        $dearSanta->mail_body = $request->string('content')->toString();
        $dearSanta->save();

        $participant->createMetric('dearSanta');
        event(new ParticipantUpdated($participant)); // their other open tabs

        $participant->santa->notify(new DearSantaNotification($dearSanta));

        return response()->json([
            'message' => trans('message.sent'),
            'email' => $this->publicDearSanta($dearSanta->refresh()),
        ]);
    }

    private function pendingEmail(Participant $participant): ?string
    {
        if ($participant->pending_email_at?->addSeconds(config()->integer('mail.email_change.code_ttl'))->isFuture() !== true) {
            return null;
        }

        return $participant->pending_email;
    }

    // Everyone but themselves and their own target (whom they already know, and give to)
    /** @return Collection<int, Participant> */
    private function suggestionRecipients(Participant $participant): Collection
    {
        $targetId = $participant->target->id;

        return $participant->draw->participants
            ->reject(fn (Participant $other): bool => $other->id === $participant->id || $other->id === $targetId)
            ->values();
    }

    /** @return SupportCollection<int, array{mail_body: string, recipient: string, signed: bool, mail: array{id: int, delivery_status: DeliveryStatus, created_at: mixed, updated_at: mixed}}> */
    private function sentSuggestions(Participant $participant): SupportCollection
    {
        return $participant->sentSuggestions()->with('recipient')->get()
            ->mapWithKeys(fn (GiftSuggestion $suggestion): array => [$suggestion->mail->id => $this->publicSuggestion($suggestion)]);
    }

    // Ideas others gave for their target, like the target's own messages (they follow a change of target)
    /** @return SupportCollection<int, array{id: int, mail_body: string, created_at: mixed, sender: string|null}> */
    private function receivedSuggestions(Participant $participant): SupportCollection
    {
        return $participant->target->receivedSuggestions()->with('sender')->get()
            ->reject(fn (GiftSuggestion $suggestion): bool => $suggestion->sender_id === $participant->id)
            ->map(fn (GiftSuggestion $suggestion): array => [
                'id' => $suggestion->mail->id,
                'mail_body' => $suggestion->mail_body,
                'created_at' => $suggestion->mail->created_at,
                // Its author only when they signed it: "De : ?" otherwise
                'sender' => $suggestion->signature(),
            ])->values();
    }

    // A message to their santa, as its sender sees it (dear_santas has no timestamps: the dates come from the mail)
    /** @return array{id: int, mail_body: string, mail: array{id: int, delivery_status: DeliveryStatus, created_at: mixed, updated_at: mixed}} */
    private function publicDearSanta(DearSanta $dearSanta): array
    {
        return [
            'id' => $dearSanta->id,
            'mail_body' => $dearSanta->mail_body,
            'mail' => $this->senderMail($dearSanta->mail),
        ];
    }

    /** @return array{mail_body: string, recipient: string, signed: bool, mail: array{id: int, delivery_status: DeliveryStatus, created_at: mixed, updated_at: mixed}} */
    private function publicSuggestion(GiftSuggestion $suggestion): array
    {
        return [
            'mail_body' => $suggestion->mail_body,
            'recipient' => $suggestion->recipient->name,
            'signed' => $suggestion->signed,
            'mail' => $this->senderMail($suggestion->mail),
        ];
    }

    // Up to "sent", never what came after: seeing when someone's santa read their email, or that it bounced,
    // would tell who that santa is. The sender could not fix a bounce anyway, the organizer resends
    /** @return array{id: int, delivery_status: DeliveryStatus, created_at: mixed, updated_at: mixed} */
    private function senderMail(Mail $mail): array
    {
        $hidden = ! $mail->delivery_status->isVisibleToSender();

        return [
            'id' => $mail->id,
            'delivery_status' => $hidden ? DeliveryStatus::Sent : $mail->delivery_status,
            'created_at' => $mail->created_at,
            'updated_at' => $hidden ? $mail->created_at : $mail->updated_at,
        ];
    }
}
