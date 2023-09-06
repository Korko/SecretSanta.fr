<?php

namespace App\Http\Controllers;

use App\Enums\DrawStatus;
use App\Models\Draw;
use App\Models\Participant;
use Closure;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class DrawDashboardController extends Controller
{
    public function index(Draw $draw): Response
    {
        // TODO
        return response('test');
    }

    public function cancel(Draw $draw): Response
    {
        $draw->status = DrawStatus::CANCELED;
        $draw->save();

        // TODO
        return response('test');
    }

    public function changeTitle(Draw $draw, Request $request): JsonResponse
    {
        // TODO
        return response()->json([
            'message' => 'foobar'
        ]);
    }

    public function changeOrganizerName(Draw $draw, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:55',
                // New organizer name should not be the same as an actual participant
                // apart from themselves if they submit the same name
                function (string $attribute, mixed $value, Closure $fail) use ($draw) {
                    $otherNames = $draw
                        ->participants
                        ->except($draw->organizer_id)
                        ->pluck('name');

                    if($otherNames->contains($value)) {
                        $fail('validation.custom.participant.name.not-unique');
                    }
                }
            ]
        ]);

        $draw->organizer_name = $validated['name'];
        $draw->save();

        if($draw->participantOrganizer) {
            $draw->organizer->name = $validated['name'];
            $draw->organizer->save();
        }

        // TODO
        return response()->json([
            'message' => 'foobar'
        ]);
    }

    public function changeOrganizerEmail(Draw $draw, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|max:320',
        ]);

        $draw->organizer_email = $validated['email'];
        $draw->organizer_email_verified_at = null;
        $draw->save();

        if($draw->participantOrganizer) {
            $draw->organizer->email = $validated['email'];
            $draw->organizer->email_verified_at = null;
            $draw->organizer->save();
        }

        // TODO
        return response()->json([
            'message' => 'foobar'
        ]);
    }

    public function confirmOrganizerEmail(Draw $draw): Response
    {
        $draw->organizer_email_verified_at = Carbon::now();
        $draw->save();

        if($draw->participantOrganizer) {
            $draw->organizer->email_verified_at = Carbon::now();
            $draw->organizer->save();
        }

        // TODO
        return response('test');
    }

    public function participate(Draw $draw): JsonResponse
    {
        throw_if($draw->participantOrganizer, new Exception('Organizer is already a participant'));

        $organizer = $draw->participants()->create([
            'ulid' => Str::ulid(),
            'name' => $draw->organizer_name,
            'email' => $draw->organizer_email,
            'email_verified_at' => $draw->organizer_email_verified_at,
        ]);

        $draw
            ->organizer()
            ->associate($organizer)
            ->save();

        // TODO
        return response()->json([
            'message' => 'foobar'
        ]);
    }

    public function withdraw(Draw $draw): JsonResponse
    {
        throw_unless($draw->participantOrganizer, new Exception('Organizer is not a participant'));

        $draw
            ->organizer
            ->delete();

        $draw
            ->organizer()
            ->dissociate()
            ->save();

        // TODO
        return response()->json([
            'message' => 'foobar'
        ]);
    }

    public function addParticipantName(Draw $draw, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:55',
                // New participant name should not be the same as the organizer name
                function (string $attribute, mixed $value, Closure $fail) use ($draw) {
                    if($draw->organizer_name === $value) {
                        $fail('validation.custom.participant.name.same-as-organizer');
                    }
                },
                // New participant name should not be the same as another participant
                function (string $attribute, mixed $value, Closure $fail) use ($draw) {
                    $otherNames = $draw
                        ->participants
                        ->pluck('name');

                    if($otherNames->contains($value)) {
                        $fail('validation.custom.participant.name.not-unique');
                    }
                }
            ],
        ]);

        $draw->participants()->create([
            'ulid' => Str::ulid(),
            // Don't use $draw->organizer_* here, as they are raw (encrypted)
            'name' => $validated['name'],
            //'email' => $validated['email'],
        ]);

        // TODO
        return response()->json([
            'message' => 'foobar'
        ]);
    }

    public function changeParticipantName(Participant $participant, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:55',
                // TODO: Non participant organizer should not have the same name as an actual participant
                function (string $attribute, mixed $value, Closure $fail) use ($participant) {
                    $otherNames = $participant
                        ->draw
                        ->participants
                        ->pluck('name')
                        ->except($participant->draw->organizer->name);

                    if($otherNames->contains($value)) {
                        $fail('validation.custom.participant.name.not-unique');
                    }
                }
            ]
        ]);

        $participant->name = $validated['name'];
        $participant->save();

        // TODO
        return response()->json([
            'message' => 'foobar'
        ]);
    }

    public function removeParticipantName(Participant $participant, Request $request): JsonResponse
    {
        $request->validate([

        ]);

        $participant->delete();

        // TODO
        return response()->json([
            'message' => 'foobar'
        ]);
    }

    public function changeParticipantEmail(Participant $participant, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|max:320',
        ]);

        $participant->email = $validated['email'];
        $participant->email_verified_at = null;
        $participant->save();

        $draw = $participant->draw;
        if($participant->is($draw->organizer)) {
            $draw->organizer_email = $validated['email'];
            $draw->organizer_email_verified_at = null;
            $draw->save();
        }

        // TODO
        return response()->json([
            'message' => 'foobar'
        ]);
    }

    public function removeParticipant(Participant $participant, Request $request): JsonResponse
    {
        $participant->delete();

        // TODO
        return response()->json([
            'message' => 'foobar'
        ]);
    }
}
