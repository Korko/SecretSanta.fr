@extends('emails/layout_plain')

@section('title')
{!! __('emails.target_changed.heading') !!}
@endsection

@section('content')
{!! __('emails.hello', ['name' => $santaName]) !!}

{!! __('emails.target_withdrawn.body', ['app' => config('app.name')]) !!}

{!! __('emails.gift.for') !!} : {!! $targetName !!}
@if($targetMessages || $giftSuggestions)

---

@include('emails/partials/target_messages_plain')
@endif
@endsection
