@extends('emails/layout_plain')

@section('title')
    {{ __('emails.target_changed.heading') }}
@endsection

@section('content')
    {{ __('emails.hello', ['name' => $santaName]) }}
    {{ __('emails.target_changed.body') }}
    {{ __('emails.target.new', ['name' => $targetName]) }}
    {{ __('emails.target.messages') }}
@endsection
