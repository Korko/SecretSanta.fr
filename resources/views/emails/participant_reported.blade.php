@extends('emails/layout')

@section('title')
    {{ __('emails.participant_reported.heading') }}
@endsection

@section('content')
    <p style="padding-bottom:10px !important">{{ __('emails.hello', ['name' => $organizerName]) }}</p>

    <p style="padding-bottom:10px !important">{{ __('emails.participant_reported.'.$reason, ['name' => $name, 'email' => $email, 'app' => config('app.name')]) }}</p>

    <p style="padding-bottom:10px !important">{!! __('emails.participant_reported.'.$reason.'_action', ['name' => e($name), 'link' => '<a href="'.e($panelLink).'">'.e($panelLink).'</a>']) !!}</p>
@endsection
