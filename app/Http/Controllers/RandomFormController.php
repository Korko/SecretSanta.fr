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
            'body' => $request->input('contentMail'),
        ];

        $smsContent = [
            'body' => $request->input('contentSMS'),
        ];

        return (new DrawHandler())->contactParticipants($participants, $hat, $mailContent, $smsContent, $dearSantaExpiration);
    }

    protected function getParticipants(Request $request)
    {
        $names = $request->input('name');
        $emails = $request->input('email');
        $phones = $request->input('phone');
        $exclusions = $request->input('exclusions', []);

        $participants = [];
        for ($i = 0; $i < count($names); $i++) {
            if ($phones[$i] && substr($phones[$i], 0, 1) === '0') {
                $phones[$i] = substr($phones[$i], 1);
            }
            $participants[$i] = [
                'name'       => $names[$i],
                'email'      => $emails[$i],
                'phone'      => $phones[$i] ? '+33'.$phones[$i] : $phones[$i],
                'exclusions' => (isset($exclusions[$i])) ? array_map('intval', $exclusions[$i]) : [],
            ];
        }

        return $participants;
    }
}
