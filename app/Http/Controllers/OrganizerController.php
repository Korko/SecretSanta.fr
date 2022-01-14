<?php

namespace App\Http\Controllers;

use App\Models\Draw;
use App\Models\Mail as MailModel;
use App\Models\Participant;
use App\Notifications\ConfirmWithdrawal;
use App\Notifications\DearSanta;
use App\Notifications\TargetDrawn;
use App\Notifications\TargetWithdrawn;
use App\Notifications\TargetNameChanged;
use Carbon\Carbon;
use Csv;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Lang;

class OrganizerController extends Controller
{
    public function view(Draw $draw)
    {
        return response()->view('organizer', [
            'draw' => $draw->hash,
        ]);
    }

    public function fetch(Draw $draw)
    {
        $drawFields = ['hash', 'mail_title', 'created_at', 'finished_at', 'deletes_at', 'next_solvable', 'organizer_name'];
        $participantFields = ['hash', 'name', 'email', 'mail' => ['id', 'updated_at', 'delivery_status']];

        if ($draw->finished) {
            $participantFields[] = ['target' => ['hash', 'name']];
        }

        return response()->json([
            'draw' => $draw->only($drawFields),
            'participants' => $draw->participants->load('mail')->mapWithKeys(function ($participant) use ($draw, $participantFields) {
                return [
                    $participant->hash => $participant->only($participantFields) + [
                        'changeEmailUrl' => $draw->finished ? '' : URL::signedRoute('organizerPanel.changeEmail', [
                            'draw' => $draw, 'participant' => $participant
                        ]),
                        'changeNameUrl' => $draw->finished ? '' : URL::signedRoute('organizerPanel.changeName', [
                            'draw' => $draw, 'participant' => $participant
                        ]),
                        'withdrawalUrl' => $draw->finished ? '' : URL::signedRoute('organizerPanel.withdraw', [
                            'draw' => $draw, 'participant' => $participant
                        ]),
                    ]
                ];
            })
        ]);
    }

    public function fetchState(Draw $draw)
    {
        return response()->json([
            'participants' => $draw->participants->load('mail')->mapWithKeys(function ($participant) {
                return [$participant->hash => $participant->only(['hash', 'mail'])];
            }),
        ]);
    }

    public function changeEmail(Request $request, Draw $draw, Participant $participant)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:320']
        ], [
            'email.required' => Lang::get('validation.custom.organizer.email.required'),
            'email.email'    => Lang::get('validation.custom.organizer.email.format'),
        ]);

        if ($participant->email === $request->input('email')) {
            $message = trans('message.sent');

            $participant->createMetric('resend_email');
        } else {
            $participant->email = $request->input('email');
            $participant->save();

            $participant->createMetric('change_email');

            $message = trans('organizer.changed');
        }

        $participant->mail->markAsCreated();

        $participant->notify(new TargetDrawn);

        return response()->json([
            'message' => $message, 'participant' => $participant->only(['hash', 'mail']),
        ]);
    }

    public function changeName(Request $request, Draw $draw, Participant $participant)
    {
        $validated = $request->validate([
            'name' => ['required', 'max:55', Rule::notIn($draw->participants->pluck('name'))],
        ], [
            'name.required' => Lang::get('validation.custom.organizer.name.required'),
            'name.not_in' => Lang::get('validation.custom.organizer.name.not_in'),
        ]);

        $participant->name = $request->input('name');
        $participant->save();

        $participant->createMetric('change_name');

        $participant->santa->notify(new TargetNameChanged);

        return response()->json([
            'message' => trans('organizer.changed'), 'participant' => $participant->only(['hash', 'name']),
        ]);
    }

    public function withdraw(Draw $draw, Participant $participant)
    {
        abort_unless($draw->participants->count() > 3, 403, Lang::get('error.withdraw'));

        $santa = $participant->santa;
        $target = $participant->target;

        // A -> B -> C => A -> C
        $santa->target()->associate($target);
        $santa->save();

        $santa->notify(new TargetWithdrawn);
        $target->dearSantas->each(function ($dearSanta) use ($santa) {
            $santa->notify(new DearSanta($dearSanta));
        });
        $participant->delete();
        $participant->notify(new ConfirmWithdrawal);

        return response()->json([
            'message' => trans('organizer.withdrawn', ['name' => $participant->name]),
        ]);
    }

    public function csvInit(Draw $draw)
    {
        $draw->createMetric('csv_initial_download');

        return response(
            "\xEF\xBB\xBF".// UTF-8 BOM
            $draw->participants
                ->toCsv(['name', 'email', 'exclusionsNames'])
                ->prepend([
                    ['# Fichier généré le '.date('d-m-Y').' sur '.config('app.name').' ('.config('app.url').')'],
                    ['# Ce fichier peut être utilisé pour préremplir les participants ainsi que les exclusions associées'],
                ]),
            200, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function csvFinal(Draw $draw)
    {
        abort_unless($draw->finished, 403, Lang::get('error.finished'));
        abort_unless($draw->next_solvable, 404, Lang::get('error.solvable'));

        $draw->createMetric('csv_final_download');

        return response(
            "\xEF\xBB\xBF".// UTF-8 BOM
            $draw->participants
                ->appendTargetToExclusions()
                ->toCsv(['name', 'email', 'exclusionsNames'])
                ->prepend([
                    ['# Fichier généré le '.date('d-m-Y').' sur '.config('app.name').' ('.config('app.url').')'],
                    ['# Ce fichier peut être utilisé pour préremplir les participants ainsi que les exclusions associées'],
                ]),
            200, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function delete(Request $request, Draw $draw)
    {
        $draw->delete();

        $draw->createMetric('delete');

        return response()->json([
            'message' => trans('organizer.deleted'),
        ]);
    }
}
