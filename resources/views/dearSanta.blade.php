@extends('templates/fetcher', ['styles' => '/css/dearSanta.css', 'fetchUrl' => URL::signedRoute('dearsanta.fetch', ['participant' => $participant])])

@section('script')
    @parent

    @javascript([
        'routes' => [
            'fetchStateUrl' => URL::signedRoute('dearsanta.fetchState', ['participant' => $participant]),
            'contactUrl' => URL::signedRoute('dearsanta.contact', ['participant' => $participant])
        ]
    ])

    <script type="text/javascript" src="{{ mix('/js/common.js') }}"></script>
    <script type="text/javascript" src="{{ mix('/js/dearSanta.js') }}"></script>
@stop
