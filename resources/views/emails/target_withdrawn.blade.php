@extends('emails/layout')

@section('title', __('emails.target_changed.heading'))

@section('content')
    <x-mail.p>{{ __('emails.hello', ['name' => $santaName]) }}</x-mail.p>
    <x-mail.p :top="8">{{ __('emails.target_withdrawn.body', ['app' => config('app.name')]) }}</x-mail.p>
    <x-mail.gift :label="__('emails.gift.for')" :name="$targetName" />
@if($targetMessages || $giftSuggestions)

    <x-mail.hr />
    @include('emails/partials/target_messages')
@endif
@endsection
