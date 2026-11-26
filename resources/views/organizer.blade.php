@extends('templates/kraftFetcher', ['fetchUrl' => URL::signedRoute('organizerPanel.fetch', ['draw' => $draw])])

@section('script')
    @parent

    @javascript([
        'routes' => [
            'csvInitUrl' => URL::signedRoute('organizerPanel.csvInit', ['draw' => $draw]),
            'csvFinalUrl' => URL::signedRoute('organizerPanel.csvFinal', ['draw' => $draw]),
            'deleteUrl' => URL::temporarySignedRoute('organizerPanel.delete', 3600, ['draw' => $draw]), // 1h validity
            'fetchStateUrl' => URL::signedRoute('organizerPanel.fetchState', ['draw' => $draw]),
            'changeParticipantsUrl' => URL::signedRoute('organizerPanel.changeParticipants', ['draw' => $draw]),
            'restoreUrl' => URL::signedRoute('organizerPanel.restore', ['draw' => $draw]),
            'changeDateUrl' => URL::signedRoute('organizerPanel.changeDate', ['draw' => $draw]),
            'changeEventDetailsUrl' => URL::signedRoute('organizerPanel.changeEventDetails', ['draw' => $draw]),
            'revealTargetsUrl' => URL::signedRoute('organizerPanel.revealTargets', ['draw' => $draw]),
            // Their own participant part, when the organizer takes part
            'participant' => $participantRoutes,
        ],
        'pusher' => [
            'key' => config('broadcasting.connections.reverb.key'),
            'host' => config('broadcasting.connections.reverb.options.host'),
            'port' => config('broadcasting.connections.reverb.options.port'),
            'scheme' => config('broadcasting.connections.reverb.options.scheme'),
        ]
    ])

    @vite('resources/js/common.js')
    @vite('resources/js/kraftMenu.js')
    @vite('resources/js/organizer.js')
@stop

