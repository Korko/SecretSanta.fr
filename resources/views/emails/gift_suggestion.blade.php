@extends('emails/layout')

@section('title', __('emails.gift_suggestion.heading', ['name' => $targetName]))

@section('content')
@if($senderName ?? null)
    <x-mail.p>{{ __('emails.gift_suggestion.from_signed', ['sender' => $senderName, 'name' => $targetName]) }}</x-mail.p>
@else
    <x-mail.p>{{ __('emails.gift_suggestion.from_anonymous', ['name' => $targetName]) }}</x-mail.p>
@endif
    {{-- Its author only when they signed it: "De : ?" otherwise --}}
    <x-mail.gift :label="__('emails.gift.from')" :name="($senderName ?? null) ?: __('emails.gift.anonymous')" />
    <x-mail.letter plain>{!! nl2br(e($content)) !!}</x-mail.letter>
    <x-mail.p>{{ __(($senderName ?? null) ? 'emails.gift_suggestion.secret_signed' : 'emails.gift_suggestion.secret', ['sender' => $senderName ?? '', 'name' => $targetName]) }}</x-mail.p>
    <x-mail.button :href="$dearSantaLink" variant="kraft">{{ __('emails.page_button') }}</x-mail.button>
@endsection
