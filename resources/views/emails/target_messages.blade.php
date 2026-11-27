@extends('emails/layout')

@section('title', __('emails.target_messages.heading'))
@section('preheader', __('emails.target_messages.preheader'))

@section('content')
    <x-mail.p>{{ __('emails.target_messages.lead') }}</x-mail.p>
    <x-mail.h2>{{ __('emails.target_messages.target') }}</x-mail.h2>
    <x-mail.gift :label="__('emails.gift.for')" :name="$targetName" />

    @include('emails/partials/target_messages')
@isset($dearSantaLink)
    <x-mail.button :href="$dearSantaLink" variant="kraft">{{ __('emails.page_button') }}</x-mail.button>
@endisset
@endsection
