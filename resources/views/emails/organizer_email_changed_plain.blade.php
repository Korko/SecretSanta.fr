@extends('emails/layout_plain')

@section('title')
    {{ __('emails.organizer_email_changed.heading') }}
@endsection

@section('content')
    {{ __('emails.hello', ['name' => $organizerName]) }}
    {{ __('emails.organizer_email_changed.body', ['app' => config('app.name'), 'email' => $newEmail]) }}
    {{ __('emails.organizer_email_changed.ignore') }}
    {{ __('emails.organizer_email_changed.otherwise') }}
@endsection
