@extends('emails/layout')

@section('title')
    {{ __('emails.participant_email_code.heading') }}
@endsection

@section('content')
    <p style="padding-bottom:10px !important">{{ __('emails.hello', ['name' => $name]) }}</p>

    <p style="padding-bottom:10px !important">{{ __('emails.participant_email_code.body', ['app' => config('app.name')]) }}</p>

    <p style="padding-bottom:10px !important;font-size:24px;font-weight:bold;letter-spacing:4px">{{ $code }}</p>

    <p style="padding-bottom:10px !important">{{ __('emails.participant_email_code.valid', ['minutes' => intdiv(config()->integer('mail.email_change.code_ttl'), 60)]) }}</p>
@endsection
