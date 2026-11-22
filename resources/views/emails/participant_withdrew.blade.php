@extends('emails/layout')

@section('title')
    {{ __('emails.participant_withdrew.heading') }}
@endsection

@section('content')
    <p style="padding-bottom:10px !important">{{ __('emails.hello', ['name' => $organizerName]) }}</p>

@if($date)
    <p style="padding-bottom:10px !important">{{ __('emails.participant_withdrew.pending', ['name' => $name, 'app' => config('app.name'), 'date' => $date]) }}</p>

    <p style="padding-bottom:10px !important">{!! __('emails.participant_withdrew.cancel', ['link' => '<a href="'.e($panelLink).'">'.e($panelLink).'</a>']) !!}</p>
@else
    <p style="padding-bottom:10px !important">{{ __('emails.participant_withdrew.body', ['name' => $name, 'app' => config('app.name')]) }}</p>

    <p style="padding-bottom:10px !important">{{ __('emails.participant_withdrew.santa', ['name' => $name]) }}</p>
@endif
@endsection
