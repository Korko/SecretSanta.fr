<?php

namespace App\Http\Controllers;

use App\Models\Draw;
use App\Models\Participant;
use App\Notifications\ConfirmWithdrawal;
use App\Notifications\DearSanta;
use App\Notifications\TargetDrawn;
use App\Notifications\TargetNameChanged;
use App\Notifications\TargetWithdrawn;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Lang;

class OrganizerController extends Controller
{
    public function index(Draw $draw)
    {
        return static::renderWithInertia('OrganizerPanel.vue', [
            'routes' => [
                'form' => URL::route('form.index'),
                'dashboard' => URL::route('dashboard'),
                'fetch' => URL::signedRoute('organizer.fetch', ['draw' => $draw]),
                'csvInitUrl' => URL::signedRoute('organizer.csvInit', ['draw' => $draw]),
                'csvFinalUrl' => URL::signedRoute('organizer.csvFinal', ['draw' => $draw]),
                'deleteUrl' => URL::temporarySignedRoute('organizer.delete', 3600, ['draw' => $draw]),
            ],
        ]);
    }

    /**
     * Return encrypted data
     */
    public function fetch(Draw $draw)
    {
        $drawFields = ['hash', 'mail_title', 'created_at', 'finished_at', 'deletes_at', 'next_solvable', 'organizer_name'];
        $participantFields = ['hash', 'name', 'email', 'mail' => ['id', 'updated_at', 'delivery_status']];

        if ($draw->isFinished) {
            $participantFields[] = ['target' => ['hash', 'name']];
        }

        return response()->json([
            'draw' => $draw->only($drawFields),
            'participants' => $draw->participants->load('mail')->mapWithKeys(function ($participant) use ($draw, $participantFields) {
                return [
                    $participant->hash => $participant->only($participantFields) + [
                        'changeEmailUrl' => $draw->isFinished ? '' : URL::signedRoute('organizer.changeEmail', [
                            'draw' => $draw, 'participant' => $participant,
                        ]),
                        'changeNameUrl' => $draw->isFinished ? '' : URL::signedRoute('organizer.changeName', [
                            'draw' => $draw, 'participant' => $participant,
                        ]),
                        'withdrawalUrl' => $draw->isFinished ? '' : URL::signedRoute('organizer.withdraw', [
                            'draw' => $draw, 'participant' => $participant,
                        ]),
                    ],
                ];
            }),
        ]);
    }

    public function changeEmail(Request $request, Draw $draw, Participant $participant)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:320'],
        ], [
            'email.required' => Lang::get('validation.custom.organizer.email.required'),
            'email.email' => Lang::get('validation.custom.organizer.email.format'),
        ]);

        if ($participant->email === $request->input('email')) {
            $participant->createMetric('resend_email');
        } else {
            $participant->email = $request->input('email');
            $participant->save();

            $participant->createMetric('change_email');
        }

        $participant->mail->markAsCreated();

        try {
            $participant->notify(new TargetDrawn);

            $response = [
                'message' => $participant->wasChanged('email') ?
                    trans('Adresse email modifiée avec succès !') :
                    trans('Email réenvoyé avec succès !'),
            ];
        } catch (Exception $e) {
            $response = [
                'error' => $participant->wasChanged('email') ?
                    trans('Adresse modifiée avec succès mais une erreur est survenue à l\'envoi de l\'email.') :
                    trans('Une erreur est survenue dans l\'envoi de l\'email. Veuillez réessayer plus tard.'),
            ];
        }

        return response()->json($response + [
            'participant' => $participant->only(['hash', 'mail']),
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

        try {
            $participant->santa->notify(new TargetNameChanged($participant));

            $response = [
                'message' => trans('Nom modifié avec succès !'),
            ];
        } catch (Exception $e) {
            $response = [
                'error' => trans('Nom modifié avec succès mais le père noël secrêt de cette personne n\'a pas pu être prévenu du changement.'),
            ];
        }

        return response()->json($response + [
            'participant' => $participant->only(['hash', 'name']),
        ]);
    }

    public function withdraw(Draw $draw, Participant $participant)
    {
        abort_unless($draw->participants->count() > 3, 403, Lang::get('error.withdraw'));

        // A -> B -> C => A -> C
        $participant->santa->target()->associate($participant->target);
        $participant->santa->save();

        try {
            $participant->santa->notify(new TargetWithdrawn($participant, $participant->target));
            $participant->target->dearSantas->each(function ($dearSanta) use ($participant) {
                $participant->santa->notify(new DearSanta($dearSanta));
            });
        } catch (Exception $e) {
            //TODO
        }

        $participant->delete();
        try {
            $participant->notify(new ConfirmWithdrawal);
        } catch (Exception $e) {
            //TODO
        }

        return response()->json([
            'message' => trans(':name ne participe plus à l\'évènement.', ['name' => $participant->name]),
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
        abort_unless($draw->isFinished, 403, Lang::get('error.finished'));
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
