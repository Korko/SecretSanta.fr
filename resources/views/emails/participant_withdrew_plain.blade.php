@extends('emails/layout_plain')

@section('title')
    {!! __('emails.participant_withdrew.heading') !!}
@endsection

@section('content')
    {!! __('emails.hello', ['name' => $organizerName]) !!}
@if($date)
    {!! __('emails.participant_withdrew.pending', ['name' => $name, 'app' => config('app.name'), 'date' => $date]) !!}
    {!! __('emails.participant_withdrew.cancel', ['link' => $panelLink]) !!}
@else
    {!! __('emails.participant_withdrew.body', ['name' => $name, 'app' => config('app.name')]) !!}
    {!! __('emails.participant_withdrew.santa', ['name' => $name]) !!}
@endif
@endsection
