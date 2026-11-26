@extends('emails/layout_plain')

@section('title')
{!! __('emails.gift_suggestion.heading', ['name' => $targetName]) !!}
@endsection

@section('content')
@if($senderName ?? null)
{!! __('emails.gift_suggestion.from_signed', ['sender' => $senderName, 'name' => $targetName]) !!}
@else
{!! __('emails.gift_suggestion.from_anonymous', ['name' => $targetName]) !!}
@endif

> {!! $content !!}

{!! __(($senderName ?? null) ? 'emails.gift_suggestion.secret_signed' : 'emails.gift_suggestion.secret', ['sender' => $senderName ?? '', 'name' => $targetName]) !!}

{!! __('emails.page_button') !!} : {!! $dearSantaLink !!}
@endsection
