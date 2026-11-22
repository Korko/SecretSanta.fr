@extends('emails/layout_plain')

@section('title')
    {!! __('emails.organizer_recap.heading') !!}
@endsection

@section('content')
    {!! __('emails.hello', ['name' => $organizerName]) !!}

    {!! __('emails.organizer_recap.thanks', ['app' => config('app.name')]) !!}

@include('emails/partials/organizer_plain', ['organizer' => get_defined_vars()])
    {!! __('emails.organizer_recap.enjoy') !!}
@endsection
