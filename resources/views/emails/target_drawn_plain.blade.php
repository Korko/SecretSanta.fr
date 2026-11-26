@extends('emails/layout_plain')

@section('title')
{!! __('emails.target_drawn.heading') !!}
@endsection

@section('content')
{!! __('emails.target_drawn.intro') !!}

> {!! $content !!}

@include('emails/partials/event_plain')

---

{!! __('emails.target_drawn.ask_title') !!}

{!! __('emails.target_drawn.ask') !!}

{!! __('emails.target_drawn.write_button') !!} : {!! $dearSantaLink !!}
@unless($organizer)

{!! __('emails.target_drawn.report') !!}
@endunless
@if($organizer)

---

@include('emails/partials/organizer_plain')
@endif
@if($targetMessages || $giftSuggestions)

---

@include('emails/partials/target_messages_plain')
@endif
@endsection
