@extends('emails/layout')

@section('title', __('emails.target_drawn.heading'))
@section('preheader', __('emails.target_drawn.preheader'))

@section('content')
    <x-mail.p>{{ __('emails.target_drawn.intro') }}</x-mail.p>
    <x-mail.letter>{!! nl2br(e($content)) !!}</x-mail.letter>

    @include('emails/partials/event')

    <x-mail.hr />
    <x-mail.h2 :top="28">{{ __('emails.target_drawn.ask_title') }}</x-mail.h2>
    <x-mail.p :top="8">{{ __('emails.target_drawn.ask') }}</x-mail.p>
    <x-mail.button :href="$dearSantaLink">{{ __(($organizer['samePage'] ?? false) ? 'emails.page_button_organizer' : 'emails.page_button') }}</x-mail.button>
@unless($organizer)
    {{-- Not to the organizer: no one to report to, and their page has no such button --}}
    <x-mail.p muted small>{{ __('emails.target_drawn.report') }}</x-mail.p>
@endunless
@if($organizer)

    @include('emails/partials/organizer')
@endif
@if($targetMessages || $giftSuggestions)

    <x-mail.hr />
    @include('emails/partials/target_messages')
@endif
@endsection
