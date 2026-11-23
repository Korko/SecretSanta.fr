@extends('emails/layout_plain')

@section('title')
    {!! __('emails.gift_suggestion.heading', ['name' => $targetName]) !!}
@endsection

@section('content')
    {!! $senderName ? __('emails.gift_suggestion.from', ['sender' => $senderName, 'name' => $targetName]) : __('emails.gift_suggestion.from_anonymous', ['name' => $targetName]) !!}
    > {!! $content !!}
    {!! __('emails.gift_suggestion.secret', ['name' => $targetName, 'link' => $dearSantaLink]) !!}
@endsection
