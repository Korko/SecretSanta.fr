@extends('emails/layout')

@section('title', __('emails.organizer_email_changed.heading'))

@section('content')
    <x-mail.p>{{ __('emails.hello', ['name' => $organizerName]) }}</x-mail.p>
    <x-mail.p :top="8">{{ __('emails.organizer_email_changed.body', ['app' => config('app.name'), 'email' => $newEmail]) }}</x-mail.p>
    <x-mail.p>{{ __('emails.organizer_email_changed.ignore') }}</x-mail.p>
    <x-mail.p>{{ __('emails.organizer_email_changed.otherwise') }}</x-mail.p>
@endsection
