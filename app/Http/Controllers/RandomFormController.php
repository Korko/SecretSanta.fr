<?php

namespace App\Http\Controllers;

use App\Exceptions\SolverException;
use App\Http\Requests\RandomFormRequest;
use App\Services\DrawHandler;
use Arr;
use Illuminate\Http\Request;

class RandomFormController extends Controller
{
    public function view()
    {
        return view('randomForm');
    }

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

        $dataExpiration = $request->input('data-expiration');

        $mailContent = [
            'title' => $request->input('title'),
            'body'  => $request->input('content-email'),
        ];

        return (new DrawHandler())->contactParticipants($participants, $mailContent, $dataExpiration);
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
}
