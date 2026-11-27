@extends('templates/kraftFetcher', ['fetchUrl' => $routes['fetchUrl']])

@section('script')
    @parent

    @javascript([
        'routes' => $routes,
        'pusher' => [
            'key' => config('broadcasting.connections.reverb.key'),
            'host' => config('broadcasting.connections.reverb.options.host'),
            'port' => config('broadcasting.connections.reverb.options.port'),
            'scheme' => config('broadcasting.connections.reverb.options.scheme'),
        ]
    ])

    @vite('resources/js/common.js')
    @vite('resources/js/kraftMenu.js')
    @vite('resources/js/dearSanta.js')
@stop
