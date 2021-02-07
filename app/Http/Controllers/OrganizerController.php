<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrganizerChangeEmailRequest;
use App\Http\Requests\OrganizerResendEmailRequest;
use App\Models\Draw;
use App\Models\Mail as MailModel;
use App\Models\Participant;
use App\Notifications\TargetDrawn;
use Csv;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

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
            'draw' => $draw->hash,
            'expires_at' => $draw->expires_at,
            'deleted_at' => $draw->deleted_at,
            'participants' => $draw->participants->mapWithKeys(function ($participant) {
                return [$participant->id => $participant->only([
                    'id', 'name', 'email', 'mail',
                ])];
            }),
            'changeEmailUrls' => $draw->participants->mapWithKeys(function ($participant) {
                return [
                    $participant->id => URL::signedRoute('organizerPanel.changeEmail', [
                        'draw' => $participant->draw, 'participant' => $participant
                    ])
                ];
            }),
            'finalCsvAvailable' => $draw->next_solvable
        ]);
    }

    public function fetchState(Draw $draw)
    {
        return response()->json([
            'participants' => $draw->participants->mapWithKeys(function ($participant) {
                return [$participant->id => $participant->only(['id', 'mail'])];
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

        $participant->mail->updateDeliveryStatus(MailModel::CREATED);

        $participant->notify(new TargetDrawn);

        return response()->json([
            'message' => $message, 'participant' => $participant->only(['id', 'mail']),
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
                ])
        );
    }

    public function csvFinal(Draw $draw)
    {
        abort_unless($draw->expired, 403, 'Cet évènement n\'est pas encore terminé');
        abort_unless($draw->next_solvable, 404, 'Cet évènement ne permet pas cette génération');

        $draw->createMetric('csv_final_download');

        return response(
            "\xEF\xBB\xBF".// UTF-8 BOM
            $draw->participants
                ->appendTargetToExclusions()
                ->toCsv(['name', 'email', 'exclusionsNames'])
                ->prepend([
                    ['# Fichier généré le '.date('d-m-Y').' sur '.config('app.name').' ('.config('app.url').')'],
                    ['# Ce fichier peut être utilisé pour préremplir les participants ainsi que les exclusions associées'],
                ])
        );
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