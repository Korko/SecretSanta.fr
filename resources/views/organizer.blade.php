@extends('templates/fetcher', ['styles' => '/css/organizer.css', 'fetchUrl' => URL::signedRoute('organizerPanel.fetch', ['draw' => $draw])])

@section('script')
    @parent

    @javascript([
        'routes' => [
            'csvInitUrl' => URL::signedRoute('organizerPanel.csvInit', ['draw' => $draw]),
            'csvFinalUrl' => URL::signedRoute('organizerPanel.csvFinal', ['draw' => $draw]),
            'deleteUrl' => URL::signedRoute('organizerPanel.delete', ['draw' => $draw]),
            'fetchStateUrl' => URL::signedRoute('organizerPanel.fetchState', ['draw' => $draw]),
        ]
    ])

    <script type="text/javascript" src="{{ mix('/js/common.js') }}"></script>
    <script type="text/javascript" src="{{ mix('/js/organizer.js') }}"></script>
@stop

