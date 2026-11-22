@extends('emails/layout')

@section('title')
    {{ __('emails.target_changed.heading') }}
@endsection

@section('content')
    <p style="padding-bottom:10px !important">{{ __('emails.hello', ['name' => $santaName]) }}</p>

    <p style="padding-bottom:10px !important">{{ __('emails.target_withdrawn.body') }}</p>

    <p style="padding-bottom:10px !important">{{ __('emails.target_withdrawn.reassured', ['app' => config('app.name')]) }}</p>

    <p style="padding-bottom:10px !important">{{ __('emails.target.new', ['name' => $targetName]) }}</p>

    <p style="padding-bottom:10px !important">{{ __('emails.target.messages') }}</p>
@endsection
