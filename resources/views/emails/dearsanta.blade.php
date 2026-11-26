@extends('emails/layout')

@section('title', __('emails.dear_santa.heading', ['name' => $targetName]))

@section('content')
    <x-mail.p>{{ __('emails.dear_santa.intro') }}</x-mail.p>
    <x-mail.gift :label="__('emails.gift.from')" :name="$targetName" />
    <x-mail.letter>{!! nl2br(e($content)) !!}</x-mail.letter>

    <x-mail.h2>{{ __('emails.dear_santa.you_too') }}</x-mail.h2>
    <x-mail.p :top="8">{{ __('emails.dear_santa.write_yours') }}</x-mail.p>
    <x-mail.button :href="$dearSantaLink" variant="kraft">{{ __('emails.target_drawn.write_button') }}</x-mail.button>
@endsection
