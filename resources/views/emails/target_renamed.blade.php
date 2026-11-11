@extends('emails/layout')

@section('title')
    {{ __('emails.target_renamed.heading') }}
@endsection

@section('content')
    <p style="padding-bottom:10px !important">{{ __('emails.target_renamed.hello', ['name' => $santaName]) }}</p>

    <p style="padding-bottom:10px !important">{{ __('emails.target_renamed.body', ['old' => $oldName, 'new' => $newName]) }}</p>

    <p style="padding-bottom:10px !important">{{ __('emails.target_renamed.same') }}</p>
@endsection
