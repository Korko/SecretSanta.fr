<?php

namespace App\Http\Controllers;

use App\Actions\ChangeParticipantEmail;
use App\Actions\ChangeParticipantName;
use App\Actions\GenerateDrawCsv;
use App\Actions\WithdrawParticipant;
use App\Models\Draw;
use App\Models\Participant;
use App\Notifications\OrganizerFinalRecap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Lang;

class OrganizerController extends Controller
{
    public function index(Draw $draw)
    {
        return response()->inertia('OrganizerPanel', [
            'routes' => [
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
                        'resendTargetUrl' => $draw->isFinished ? '' : URL::signedRoute('organizer.resendTarget', [
                            'draw' => $draw, 'participant' => $participant,
                        ]),
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

    public function resendTarget(Draw $draw, Participant $participant)
    {
        return response()->jsonTry(
            function () use ($participant) {
                app(SendTargetToParticipant::class)->send($participant);

                $participant->createMetric('resend_target_email');

                return [
                    'participant' => $participant->only(['hash', 'mail']),
                ];
            },
            trans('Email réenvoyé avec succès !'),
            trans('Une erreur est survenue dans l\'envoi de l\'email. Veuillez réessayer plus tard.')
        );
    }

    public function changeEmail(Request $request, Draw $draw, Participant $participant)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:320'],
        ], [
            'email.required' => Lang::get('validation.custom.organizer.email.required'),
            'email.email' => Lang::get('validation.custom.organizer.email.format'),
        ]);

        return response()->jsonTry(
            function () use ($participant, $request) {
                app(ChangeParticipantEmail::class)->change($participant, $request->input('email'));

                $participant->createMetric('change_email');

                return [
                    'participant' => $participant->only(['hash', 'mail']),
                ];
            },
            trans('Adresse email modifiée avec succès !'),
            trans('Adresse modifiée avec succès mais une erreur est survenue à l\'envoi de l\'email.')
        );
    }

    public function changeName(Request $request, Draw $draw, Participant $participant)
    {
        $request->validate([
            'name' => ['required', 'max:55', Rule::notIn($draw->participants->pluck('name'))],
        ], [
            'name.required' => Lang::get('validation.custom.organizer.name.required'),
            'name.not_in' => Lang::get('validation.custom.organizer.name.not_in'),
        ]);

        return response()->jsonTry(
            function () use ($participant, $request) {
                app(ChangeParticipantName::class)->change($participant, $request->input('name'));

                $participant->createMetric('change_name');

                return [
                    'participant' => $participant->only(['hash', 'name']),
                ];
            },
            trans('Nom modifié avec succès !'),
            trans('Nom modifié avec succès mais le père noël secrêt de cette personne n\'a pas pu être prévenu du changement.')
        );
    }

    public function withdraw(Draw $draw, Participant $participant)
    {
        abort_unless($draw->participants->count() > 3, 403, Lang::get('error.withdraw'));

        app(WithdrawParticipant::class)->withdraw($participant);

        return response()->json([
            'message' => trans(':name ne participe plus à l\'évènement.', ['name' => $participant->name]),
        ]);
    }

    public function csvInit(Draw $draw)
    {
        $draw->createMetric('csv_initial_download');

        return response(
            content: app(GenerateDrawCsv::class)->generateInitial($draw),
            headers: ['Content-Type' => 'text/csv; charset=UTF-8']
        );
    }

    public function csvFinal(Draw $draw)
    {
        abort_unless($draw->isFinished, 403, Lang::get('error.finished'));
        abort_unless($draw->next_solvable, 404, Lang::get('error.solvable'));

        $draw->createMetric('csv_final_download');

        $draw->organizer->notify(new OrganizerFinalRecap($draw));

        return response(
            content: app(GenerateDrawCsv::class)->generateFinal($draw),
            headers: ['Content-Type' => 'text/csv; charset=UTF-8']
        );
    }

    public function delete(Draw $draw)
    {
        $draw->delete();

        $draw->createMetric('delete');

        return response()->json([
            'message' => trans('organizer.deleted'),
        ]);
    }
}
