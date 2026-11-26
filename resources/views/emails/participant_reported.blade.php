@extends('emails/layout')

@section('title', __('emails.participant_reported.heading'))

@section('content')
    <x-mail.p>{{ __('emails.hello', ['name' => $organizerName]) }}</x-mail.p>
    <x-mail.p :top="8">{{ __('emails.participant_reported.'.$reason, ['name' => $name, 'email' => $email, 'app' => config('app.name')]) }}</x-mail.p>
    <x-mail.p>{{ __('emails.participant_reported.'.$reason.'_action', ['name' => $name]) }}</x-mail.p>
    <x-mail.button :href="$panelLink" variant="kraft">{{ __('emails.organizer.button') }}</x-mail.button>
@endsection
