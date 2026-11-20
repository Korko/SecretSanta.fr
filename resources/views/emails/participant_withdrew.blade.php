@extends('emails/layout')

@section('title')
    {{ __('emails.participant_withdrew.heading') }}
@endsection

@section('content')
    <p style="padding-bottom:10px !important">{{ __('emails.hello', ['name' => $organizerName]) }}</p>

    <p style="padding-bottom:10px !important">{{ __('emails.participant_withdrew.body', ['name' => $name, 'app' => config('app.name')]) }}</p>

    <p style="padding-bottom:10px !important">{{ __('emails.participant_withdrew.santa', ['name' => $name]) }}</p>
@endsection
