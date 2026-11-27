@extends('emails/layout')

@section('title', __('emails.organizer_recap.heading'))

@section('content')
    <x-mail.p>{{ __('emails.hello', ['name' => $organizerName]) }}</x-mail.p>
    <x-mail.p :top="8">{{ __('emails.organizer_recap.thanks', ['app' => config('app.name'), 'count' => $participantsCount]) }}</x-mail.p>


    @include('emails/partials/organizer', ['organizer' => get_defined_vars(), 'inPanel' => false])
@endsection
