@extends('emails/layout_plain')

@section('title')
    {!! __('emails.participant_reported.heading') !!}
@endsection

@section('content')
    {!! __('emails.hello', ['name' => $organizerName]) !!}

    {!! __('emails.participant_reported.'.$reason, ['name' => $name, 'email' => $email, 'app' => config('app.name')]) !!}

    {!! __('emails.participant_reported.'.$reason.'_action', ['name' => $name, 'link' => $panelLink]) !!}
@endsection
