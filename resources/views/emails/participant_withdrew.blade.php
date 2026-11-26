@extends('emails/layout')

@section('title', __('emails.participant_withdrew.heading', ['name' => $name]))

@section('content')
    <x-mail.p>{{ __('emails.hello', ['name' => $organizerName]) }}</x-mail.p>
    <x-mail.p :top="8">{{ __('emails.participant_withdrew.body', ['name' => $name, 'app' => config('app.name')]) }}</x-mail.p>
    <x-mail.p>{{ __('emails.participant_withdrew.santa', ['name' => $name]) }}</x-mail.p>
    <x-mail.p>{{ __('emails.participant_withdrew.restore', ['name' => $name]) }}</x-mail.p>
@if($panelLink)
    <x-mail.button :href="$panelLink" variant="kraft">{{ __('emails.organizer.button') }}</x-mail.button>
@endif
@endsection
