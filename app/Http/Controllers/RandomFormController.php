<?php

namespace App\Http\Controllers;

use Arr;
use Metrics;
use App\Draw;
use App\Participant;
use Illuminate\Http\Request;
use App\Notifications\DrawCreated;
use App\Notifications\TargetDrawn;
use App\Exceptions\SolverException;
use App\Services\SymmetricalEncrypter;
use App\Http\Requests\RandomFormRequest;
use Facades\App\Services\HatSolver as Solver;

class RandomFormController extends Controller
{
    public function handle(RandomFormRequest $request)
    {
        try {
            $this->drawAndInform($request);

            $message = trans('message.sent');

            return $request->ajax() ?
                response()->json(['message' => $message]) :
                redirect('/')->with('message', $message);
        } catch (SolverException $e) {
            $error = trans('error.solution');

            return $request->ajax() ?
                response()->json(['error' => $error], 500) :
                redirect('/')->with('error', $error);
        }
    }

    protected function drawAndInform(Request $request)
    {
        $participants = $this->formatParticipants($request->input('participants'));
        $hat = Solver::one($participants, array_column($participants, 'exclusions'));

        Metrics::increment('draws');
        Metrics::increment('participants', count($participants));

        $dearSanta = ($request->input('dearsanta') === '1');

        $dataExpiration = $request->input('data-expiration');

        $mailContent = [
            'title' => $request->input('title'),
            'body'  => $request->input('content-email'),
        ];

        $smsContent = [
            'body' => $request->input('content-sms'),
        ];

        return $this->contactParticipants($participants, $hat, $mailContent, $smsContent, $dataExpiration, $dearSanta);
    }

    protected function formatParticipants(array $participants): array
    {
        for ($i = 0; $i < count($participants); $i++) {
            $participant = &$participants[$i];

            if (! empty($participant['phone'])) {
                if (substr($participant['phone'], 0, 1) === '0') {
                    $participant['phone'] = substr($participant['phone'], 1);
                }
                $participant['phone'] = '+33'.$participant['phone'];
            }

            $participant['exclusions'] = array_map('intval', Arr::get($participant, 'exclusions', []));
        }
        unset($participant);

        return $participants;
    }

    public function contactParticipants(array $participants, array $hat, array $mailContent, array $smsContent, $dataExpiration, $dearSanta = false)
    {
        $orgaSymKey = SymmetricalEncrypter::generateKey(config('app.cipher'));

        $draw = new Draw();
        $draw->encryptionKey = $orgaSymKey; // Have to be very first attribute set
        $draw->expiration = $dataExpiration;
        $draw->email_title = $mailContent['title'];
        $draw->email_body = $mailContent['body'];
        $draw->sms_body = $smsContent['body'];
        $draw->dear_santa = $dearSanta;
        $draw->save();

        foreach ($hat as $santaIdx => $targetIdx) {
            $santa = ['id' => $santaIdx] + $participants[$santaIdx];
            $target = ['id' => $targetIdx] + $participants[$targetIdx];

            $participant = new Participant();
            $participant->encryptionKey = $orgaSymKey; // Have to be very first attribute set
            $participant->draw_id = $draw->id;
            $participant->name = $santa['name'];
            $participant->email_address = Arr::get($santa, 'email');
            $participant->phone_number = Arr::get($santa, 'phone');
            $participant->target = $target;
            $participant->save();

            $superSanta = $participants[array_search($santa['id'], $hat)];

            if ($santaIdx === 0) {
                $participant->notify(new DrawCreated($draw));
            }

            $participant->notify(new TargetDrawn($draw, $superSanta));
        }
    }
}
