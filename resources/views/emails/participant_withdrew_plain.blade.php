@extends('emails/layout_plain')

@section('title')
    {!! __('emails.participant_withdrew.heading') !!}
@endsection

@section('content')
    {!! __('emails.hello', ['name' => $organizerName]) !!}
    {!! __('emails.participant_withdrew.body', ['name' => $name, 'app' => config('app.name')]) !!}
    {!! __('emails.participant_withdrew.santa', ['name' => $name]) !!}
@endsection
