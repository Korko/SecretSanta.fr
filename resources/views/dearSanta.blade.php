@extends('templates/fetcher', ['styles' => '/css/dearSanta.css', 'fetchUrl' => URL::signedRoute('dearsanta.fetch', ['participant' => $participant])])

@section('script')
    @parent

    @javascript([
        'routes' => [
            'contactUrl' => URL::signedRoute('dearsanta.contact', ['participant' => $participant]),
            'fetchStateUrl' => URL::signedRoute('dearsanta.fetchState', ['participant' => $participant]),
        ]
    ])

    @vite('resources/js/common.js')
    @vite('resources/js/dearSanta.js')
@stop
