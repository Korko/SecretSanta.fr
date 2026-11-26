@extends('emails/layout_plain')

@section('title')
{!! __('emails.gift_suggestion.heading', ['name' => $targetName]) !!}
@endsection

@section('content')
{!! __('emails.gift_suggestion.from_anonymous', ['name' => $targetName]) !!}

> {!! $content !!}

{!! __('emails.gift_suggestion.secret', ['name' => $targetName]) !!}

{!! __('emails.page_button') !!} : {!! $dearSantaLink !!}
@endsection
