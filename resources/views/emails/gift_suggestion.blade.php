@extends('emails/layout')

@section('title', __('emails.gift_suggestion.heading', ['name' => $targetName]))

@section('content')
    <x-mail.p>{{ __('emails.gift_suggestion.from_anonymous', ['name' => $targetName]) }}</x-mail.p>
    <x-mail.gift :label="__('emails.gift.from')" :name="__('emails.gift.anonymous')" />
    <x-mail.letter plain>{!! nl2br(e($content)) !!}</x-mail.letter>
    <x-mail.p>{{ __('emails.gift_suggestion.secret', ['name' => $targetName]) }}</x-mail.p>
    <x-mail.button :href="$dearSantaLink" variant="kraft">{{ __('emails.page_button') }}</x-mail.button>
@endsection
