<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrganizerChangeEmailRequest;
use App\Http\Requests\OrganizerResendEmailRequest;
use App\Models\Draw;
use App\Models\Mail as MailModel;
use App\Models\Participant;
use App\Notifications\DearSanta;
use App\Notifications\TargetDrawn;
use App\Notifications\TargetWithdrawn;
use Csv;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
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
        return response()->json([
            'draw' => $draw->only(['hash', 'mail_title', 'created_at', 'expires_at', 'deleted_at', 'next_solvable', 'organizer_name']),
            'participants' => $draw->participants->load('mail')->mapWithKeys(function ($participant) {
                return [
                    $participant->hash => $participant->only([
                        'hash', 'name', 'email', 'mail',
                    ])
                ];
            }),
            'changeEmailUrls' => $draw->participants->mapWithKeys(function ($participant) {
                return [
                    $participant->hash => URL::signedRoute('organizerPanel.changeEmail', [
                        'draw' => $participant->draw, 'participant' => $participant
                    ])
                ];
            }),
            'withdrawalUrls' => $draw->participants->mapWithKeys(function ($participant) {
                return [
                    $participant->hash => URL::signedRoute('organizerPanel.withdraw', [
                        'draw' => $participant->draw, 'participant' => $participant
                    ])
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

    public function changeEmail(OrganizerChangeEmailRequest $request, Draw $draw, Participant $participant)
    {
        if ($participant->email === $request->input('email')) {
            $message = trans('message.sent');

            $participant->createMetric('resend_email');
        } else {
            $participant->email = $request->input('email');
            $participant->save();

            $participant->createMetric('change_email');

            $message = trans('organizer.up_and_sent');
        }

        $participant->mail->markAsCreated();

        $participant->notify(new TargetDrawn);

        return response()->json([
            'message' => $message, 'participant' => $participant->only(['hash', 'mail']),
        ]);
    }

    public function withdraw(Draw $draw, Participant $participant)
    {
        abort_unless($draw->participants->count() > 3, 403, Lang::get('error.withdraw'));

        $santa = $participant->santa;
        $target = $participant->target;

        // A -> B -> C => A -> C
        $santa->target()->save($target);

        $santa->notify(new TargetWithdrawn);
        $target->dearSantas->each(function ($dearSanta) use ($santa) {
            $santa->notify(new DearSanta($dearSanta));
        });
        $participant->delete();

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
        abort_unless($draw->expired, 403, Lang::get('error.expired'));
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
