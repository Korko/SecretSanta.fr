@extends('templates/fetcher', ['fetchUrl' => URL::signedRoute('organizerPanel.fetch', ['draw' => $draw])])

@section('script')
    @parent

    @javascript([
        'routes' => [
            'csvInitUrl' => URL::signedRoute('organizerPanel.csvInit', ['draw' => $draw]),
            'csvFinalUrl' => URL::signedRoute('organizerPanel.csvFinal', ['draw' => $draw]),
            'deleteUrl' => URL::temporarySignedRoute('organizerPanel.delete', 3600, ['draw' => $draw]), // 1h validity
            'fetchStateUrl' => URL::signedRoute('organizerPanel.fetchState', ['draw' => $draw]),
            'changeParticipantsUrl' => URL::signedRoute('organizerPanel.changeParticipants', ['draw' => $draw]),
            'changeDateUrl' => URL::signedRoute('organizerPanel.changeDate', ['draw' => $draw]),
            'revealTargetsUrl' => URL::signedRoute('organizerPanel.revealTargets', ['draw' => $draw]),
        ],
        'pusher' => [
            'key' => config('broadcasting.connections.reverb.key'),
            'host' => config('broadcasting.connections.reverb.options.host'),
            'port' => config('broadcasting.connections.reverb.options.port'),
            'scheme' => config('broadcasting.connections.reverb.options.scheme'),
        ]
    ])

    @vite('resources/js/common.js')
    @vite('resources/js/organizer.js')
@stop

