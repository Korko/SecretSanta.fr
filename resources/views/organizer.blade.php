@extends('templates/fetcher', ['fetchUrl' => URL::signedRoute('organizerPanel.fetch', ['draw' => $draw])])

@section('script')
    @parent

    @javascript([
        'routes' => [
            'csvInitUrl' => URL::signedRoute('organizerPanel.csvInit', ['draw' => $draw]),
            'csvFinalUrl' => URL::signedRoute('organizerPanel.csvFinal', ['draw' => $draw]),
            'deleteUrl' => URL::temporarySignedRoute('organizerPanel.delete', 3600, ['draw' => $draw]), // 1h validity
            'fetchStateUrl' => URL::signedRoute('organizerPanel.fetchState', ['draw' => $draw]),
        ]
    ])

    <script type="text/javascript" src="{{ mix('../js/common.js') }}"></script>
    <script type="text/javascript" src="{{ mix('../js/organizer.js') }}"></script>
@stop

