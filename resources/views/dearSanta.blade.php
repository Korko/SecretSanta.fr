@extends('templates/fetcher', ['fetchUrl' => URL::signedRoute('dearSanta.fetch', ['participant' => $participant])])

@section('script')
    @parent

    @javascript([
        'routes' => [
            'contactUrl' => URL::signedRoute('dearSanta.contact', ['participant' => $participant]),
            'fetchStateUrl' => URL::signedRoute('dearSanta.fetchState', ['participant' => $participant]),
            'subUrl' => URL::signedRoute('participant.sub', ['participant' => $participant]),
            'unsubUrl' => URL::signedRoute('participant.unsub', ['participant' => $participant]),
        ]
    ])

    <script type="text/javascript" src="{{ mix('/js/common.js') }}"></script>
    <script type="text/javascript" src="{{ mix('/js/dearSanta.js') }}"></script>
@stop
