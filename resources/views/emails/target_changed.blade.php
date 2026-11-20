@extends('emails/layout')

@section('title')
    {{ __('emails.target_changed.heading') }}
@endsection

@section('content')
    <p style="padding-bottom:10px !important">{{ __('emails.hello', ['name' => $santaName]) }}</p>

    <p style="padding-bottom:10px !important">{{ __('emails.target_changed.body') }}</p>

    <p style="padding-bottom:10px !important">{{ __('emails.target.new', ['name' => $targetName]) }}</p>

    <p style="padding-bottom:10px !important">{{ __('emails.target.messages') }}</p>
@endsection
