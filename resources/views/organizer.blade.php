@extends('templates/layout', ['styles' => '/css/organizer.css'])

@section('header')
    @parent

    <!-- config js -->
    @javascript([
        'challenge' => $challenge,
        'text' => config('app.challenge')
    ])
@stop

@section('body')

@stop
