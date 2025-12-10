@extends('templates/fetcher', ['fetchUrl' => URL::signedRoute('dearSanta.fetch', ['participant' => $participant])])

@section('script')
    @parent

    @javascript([
        'routes' => [
            'contactUrl' => URL::signedRoute('dearSanta.contact', ['participant' => $participant]),
            'fetchStateUrl' => URL::signedRoute('dearSanta.fetchState', ['participant' => $participant]),
            'subUrl' => URL::signedRoute('participant.sub', ['participant' => $participant]),
            'unsubUrl' => URL::signedRoute('participant.unsub', ['participant' => $participant]),
        ],
        'pusher' => [
            'key' => Arr::get(config('websockets.apps'), '0.key'),
            'host' => Arr::get(config('websockets.apps'), '0.host'),
        ]
    ])

    @vite('resources/js/common.js')
    @vite('resources/js/dearSanta.js')
@stop
