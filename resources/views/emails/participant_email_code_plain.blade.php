@extends('emails/layout_plain')

@section('title')

{!! __('emails.participant_email_code.heading') !!}
@endsection

@section('content')

{!! __('emails.hello', ['name' => $name]) !!}

{!! __('emails.participant_email_code.body', ['app' => config('app.name')]) !!}

{!! $code !!}

{!! __('emails.participant_email_code.valid', ['minutes' => intdiv(config()->integer('mail.email_change.code_ttl'), 60)]) !!}
@endsection
