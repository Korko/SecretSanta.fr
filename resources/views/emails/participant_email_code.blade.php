@extends('emails/layout')

@section('title', __('emails.participant_email_code.heading'))

@section('content')
    <x-mail.p>{{ __('emails.hello', ['name' => $name]) }}</x-mail.p>
    <x-mail.p :top="8">{{ __('emails.participant_email_code.body', ['app' => config('app.name')]) }}</x-mail.p>
    <x-mail.code>{{ $code }}</x-mail.code>
    <x-mail.p>{{ __('emails.participant_email_code.valid', ['minutes' => intdiv(config()->integer('mail.email_change.code_ttl'), 60)]) }}</x-mail.p>
@endsection
