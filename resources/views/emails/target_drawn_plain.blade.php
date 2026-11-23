@extends('emails/layout_plain')

@section('title')
    {!! __('emails.target_drawn.heading') !!}
@endsection

@section('content')
    > {!! $content !!}

    ---

    {!! __('emails.target_drawn.write', ['link' => $dearSantaLink]) !!}

@if($organizer)

    ---

    {!! __('emails.target_drawn.as_organizer') !!} {!! __('emails.target_drawn.do_not_forward') !!}

    @include('emails/partials/organizer_plain')
@else
    {!! __('emails.target_drawn.report') !!}
@endif
@if($targetMessages || $giftSuggestions)

    ---

    @include('emails/partials/target_messages_plain')
@endif
@endsection
