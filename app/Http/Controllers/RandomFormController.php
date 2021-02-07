<?php

namespace App\Http\Controllers;

use App\Http\Requests\RandomFormRequest;
use Arr;
use DrawHandler;
use Illuminate\Http\Request;

class RandomFormController extends Controller
{
    public function view()
    {
        return response()->view('randomForm');
    }

    public function handle(RandomFormRequest $request)
    {
        $this->drawAndInform($request);

        return response()->json([
            'message' => trans('message.sent')
        ]);
    }

    protected function drawAndInform(Request $request)
    {
        $participants = $this->formatParticipants($request->input('participants'));

        $dataExpiration = $request->input('data-expiration');

        $title = $request->input('title');
        $body = $request->input('content-email');

        DrawHandler::toParticipants($participants)
            ->expiresAt($dataExpiration)
            ->notify($title, $body);
    }

    protected function formatParticipants(array $participants): array
    {
        $totalParticipants = count($participants);
        for ($i = 0; $i < $totalParticipants; $i++) {
            $participant = &$participants[$i];

            $participant['exclusions'] = array_map('intval', Arr::get($participant, 'exclusions', []));
        }

        return $participants;
    }

    public function faq()
    {
        return response()->view('faq', [
            'questions' => __('faq.questions'),
        ]);
    }
}
