@extends('emails/layout')

@section('title')
    {{ __('emails.organizer_email_changed.heading') }}
@endsection

@section('content')
    <p style="padding-bottom:10px !important">{{ __('emails.hello', ['name' => $organizerName]) }}</p>

    <p style="padding-bottom:10px !important">{{ __('emails.organizer_email_changed.body', ['app' => config('app.name'), 'email' => $newEmail]) }}</p>

    <p style="padding-bottom:10px !important">{{ __('emails.organizer_email_changed.ignore') }}</p>

    <p style="padding-bottom:10px !important">{{ __('emails.organizer_email_changed.otherwise') }}</p>
@endsection
