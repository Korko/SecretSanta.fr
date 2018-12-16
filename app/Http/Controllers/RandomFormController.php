<?php

namespace App\Http\Controllers;

use App\Exceptions\SolverException;
use App\Http\Requests\RandomFormRequest;
use App\Services\DrawHandler;
use Facades\App\Services\HatSolver as Solver;
use Illuminate\Http\Request;
use Metrics;

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
        $participants = $this->getParticipants($request);
        $hat = Solver::one($participants, array_column($participants, 'exclusions'));

        Metrics::increment('draws');
        Metrics::increment('participants', count($participants));

        $dearSantaExpiration = null;
        if ($request->input('dearsanta')) {
            $dearSantaExpiration = $request->input('dearsanta-expiration');
        }

        $mailContent = [
            'title' => $request->input('title'),
            'body'  => $request->input('contentMail'),
        ];

        $smsContent = [
            'body' => $request->input('contentSMS'),
        ];

        return (new DrawHandler())->contactParticipants($participants, $hat, $mailContent, $smsContent, $dearSantaExpiration);
    }

    protected function getParticipants(Request $request)
    {
        $participants = $request->input('participants');
        for ($i = 0; $i < count($participants); $i++) {
            $participant = &$participants[$i];

            if (!empty($participant['phone'])) {
                if (substr($participant['phone'], 0, 1) === '0') {
                    $participant['phone'] = substr($participant['phone'], 1);
                }
                $participant['phone'] = '+33'.$participant['phone'];
            }

            $participant['exclusions'] = array_map('intval', array_get($participant, 'exclusions', []));
        }
        unset($participant);

        return $participants;
    }
}
