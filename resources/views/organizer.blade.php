@extends('templates/fetcher', ['styles' => '/css/organizer.css', 'fetchUrl' => URL::signedRoute('organizerPanel.fetch', ['draw' => $draw])])

@section('script')
    @parent

    @javascript([
        'routes' => [
            'csvUrl' => URL::signedRoute('organizerPanel.csv', ['draw' => $draw]),
            'fetchStateUrl' => URL::signedRoute('organizerPanel.fetchState', ['draw' => $draw]),
            'deleteUrl' => URL::signedRoute('organizerPanel.delete', ['draw' => $draw]),
        ]
    ])

    <script type="text/javascript" src="{{ mix('/js/common.js') }}"></script>
    <script type="text/javascript" src="{{ mix('/js/organizer.js') }}"></script>
@stop

