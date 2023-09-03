<?php

namespace App\Http\Controllers;

use App\Enums\EmailAddressStatus;
use App\Enums\PendingDrawStatus;
use App\Models\PendingDraw;
use App\Models\PendingParticipant;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PendingDrawDashboardController extends Controller
{
    public function index(PendingDraw $pending): Response
    {
        // TODO
        return response('test');
    }

    public function cancel(PendingDraw $pending): Response
    {
        $pending->status = PendingDrawStatus::CANCELED;
        $pending->save();

        // TODO
        return response('test');
    }

    public function changeTitle(PendingDraw $pending, Request $request): JsonResponse
    {
        // TODO
        return response()->json([
            'message' => 'foobar'
        ]);
    }

    public function changeOrganizerName(PendingDraw $pending, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:55',
                // New organizer name should not be the same as an actual participant
                // apart from themselves if they submit the same name
                function (string $attribute, mixed $value, Closure $fail) use ($pending) {
                    $otherNames = $pending
                        ->participants
                        ->except($pending->organizer_id)
                        ->pluck('name');

                    if($otherNames->contains($value)) {
                        $fail('validation.custom.pending.participant.name.not-unique');
                    }
                }
            ]
        ]);

        $pending->organizer_name = $validated['name'];
        $pending->save();

        if($pending->participantOrganizer) {
            $pending->organizer->name = $validated['name'];
            $pending->organizer->save();
        }

        // TODO
        return response()->json([
            'message' => 'foobar'
        ]);
    }

    public function changeOrganizerEmail(PendingDraw $pending, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|max:320',
        ]);

        $pending->organizer_email = $validated['email'];
        $pending->email_status = EmailAddressStatus::CREATED;
        $pending->save();

        if($pending->participantOrganizer) {
            $pending->organizer->email = $validated['email'];
            $pending->organizer->email_status = EmailAddressStatus::CREATED;
            $pending->organizer->save();
        }

        // TODO
        return response()->json([
            'message' => 'foobar'
        ]);
    }

    public function confirmOrganizerEmail(PendingDraw $pending): Response
    {
        $pending->email_status = EmailAddressStatus::CONFIRMED;
        $pending->save();

        if($pending->participantOrganizer) {
            $pending->organizer->email_status = EmailAddressStatus::CONFIRMED;
            $pending->organizer->save();
        }

        // TODO
        return response('test');
    }

    public function addParticipantName(PendingDraw $pending, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:55',
                // New participant name should not be the same as the organizer name
                function (string $attribute, mixed $value, Closure $fail) use ($pending) {
                    if($pending->organizer_name === $value) {
                        $fail('validation.custom.pending.participant.name.same-as-organizer');
                    }
                },
                // New participant name should not be the same as another participant
                function (string $attribute, mixed $value, Closure $fail) use ($pending) {
                    $otherNames = $pending
                        ->participants
                        ->pluck('name');

                    if($otherNames->contains($value)) {
                        $fail('validation.custom.pending.participant.name.not-unique');
                    }
                }
            ],
        ]);

        $pending->participants()->create([
            // Don't use $pending->organizer_* here, as they are encrypted
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // TODO
        return response()->json([
            'message' => 'foobar'
        ]);
    }

    public function changeParticipantName(PendingParticipant $pendingParticipant, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:55',
                // TODO: Non participant organizer should not have the same name as an actual participant
                function (string $attribute, mixed $value, Closure $fail) use ($pendingParticipant) {
                    $otherNames = $pendingParticipant
                        ->pendingDraw
                        ->participants
                        ->except($pendingParticipant->id)
                        ->pluck('name');

                    if($otherNames->contains($value)) {
                        $fail('validation.custom.pending.participant.name.not-unique');
                    }
                }
            ]
        ]);

        $pendingParticipant->name = $validated['name'];
        $pendingParticipant->save();

        // TODO
        return response()->json([
            'message' => 'foobar'
        ]);
    }

    public function removeParticipantName(PendingParticipant $pendingParticipant, Request $request): JsonResponse
    {
        $request->validate([

        ]);

        $pendingParticipant->delete();

        // TODO
        return response()->json([
            'message' => 'foobar'
        ]);
    }

    public function changeParticipantEmail(PendingParticipant $pendingParticipant, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|max:320',
        ]);

        $pendingParticipant->email = $validated['email'];
        $pendingParticipant->email_status = EmailAddressStatus::CREATED;
        $pendingParticipant->save();

        $pending = $pendingParticipant->pendingDraw;
        if($pendingParticipant->is($pending->organizer)) {
            $pending->organizer_email = $validated['email'];
            $pending->email_status = EmailAddressStatus::CREATED;
            $pending->save();
        }

        // TODO
        return response()->json([
            'message' => 'foobar'
        ]);
    }

    public function removeParticipant(PendingParticipant $pendingParticipant, Request $request): JsonResponse
    {
        $pendingParticipant->delete();

        // TODO
        return response()->json([
            'message' => 'foobar'
        ]);
    }
}
