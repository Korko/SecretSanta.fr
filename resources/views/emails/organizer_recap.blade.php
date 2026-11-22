@extends('emails/layout')

@section('title')
    {{ __('emails.organizer_recap.heading') }}
@endsection

@section('content')
    <p style="padding-bottom:10px !important">{{ __('emails.hello', ['name' => $organizerName]) }}</p>

    <p style="padding-bottom:10px !important">{!! __('emails.organizer_recap.thanks', ['app' => '<a href="'.e(config('app.url')).'">'.e(config('app.name')).'</a>']) !!}</p>

    @include('emails/partials/organizer', ['organizer' => get_defined_vars()])

    <p style="padding-bottom:10px !important">{{ __('emails.organizer_recap.enjoy') }}</p>
@endsection
