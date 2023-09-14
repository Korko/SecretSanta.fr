<?php

namespace App\Http\Controllers;

use App\Enums\DrawStatus;
use App\Http\Requests\AddNameRequest;
use App\Models\Draw;
use App\Models\Participant;
use App\Notifications\DrawTitleChanged;
use App\Notifications\OrganizerNameChanged;
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
        $validated = $request->validate([
            'title' => 'required|string|max:36773',
        ]);

        $draw->title = $validated['title'];
        $draw->save();

        $draw->participantsNonOrganizer->each(function (Participant $participant) use ($draw) {
            $participant->notify(new DrawTitleChanged($draw));
        });

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

        if ($draw->participantOrganizer) {
            $draw->organizer->name = $validated['name'];
            $draw->organizer->save();
        }

        $draw->participantsNonOrganizer->each(function (Participant $participant) use ($draw) {
            $participant->notify(new OrganizerNameChanged($draw));
        });

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

        if ($draw->participantOrganizer) {
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

        if ($draw->participantOrganizer) {
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

    public function addParticipantName(Draw $draw, AddNameRequest $request): JsonResponse
    {
        $draw->participants()->create([
            'ulid' => Str::ulid(),
            'name' => $request->safe()->name,
        ]);

        // TODO
        return response()->json([
            'message' => 'foobar'
        ]);
    }

    public function changeParticipantName(Draw $draw, Participant $participant, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:55',
                // TODO: Non participant organizer should not have the same name as an actual participant
                function (string $attribute, mixed $value, Closure $fail) use ($draw, $participant) {
                    $otherNames = $draw
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

    public function removeParticipantName(Draw $draw, Participant $participant, Request $request): JsonResponse
    {
        throw_if($participant->is($draw->organizer), new Exception('Cannot remove organizer'));

        $participant->delete();

        // TODO
        return response()->json([
            'message' => 'foobar'
        ]);
    }

    public function changeParticipantEmail(Draw $draw, Participant $participant, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|max:320',
        ]);

        $participant->email = $validated['email'];
        $participant->email_verified_at = null;
        $participant->save();

        if ($participant->is($draw->organizer)) {
            $draw->organizer_email = $validated['email'];
            $draw->organizer_email_verified_at = null;
            $draw->save();
        }

        // TODO
        return response()->json([
            'message' => 'foobar'
        ]);
    }

    public function confirmParticipantEmail(Draw $draw, Participant $participant): Response
    {
        $participant->email_verified_at = Carbon::now();
        $participant->save();

        $user = $participant->user;
        if ($user === null) {
            $user = $participant->user()->create();
        }

        $user->emails()->firstOrCreate([
            'email' => $participant->email,
            'email_verified_at' => Carbon::now(),
        ]);

        // TODO
        return response('test');
    }
}
