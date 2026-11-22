@extends('emails/layout_plain')

@section('title')
    {{ __('emails.target_drawn.heading') }}
@endsection

@section('content')
    > {{ $content }}

    ---

    {{ __('emails.target_drawn.write', ['link' => $dearSantaLink]) }}

    {{ __('emails.target_drawn.report') }}
@if($organizer)

    ---

    {{ __('emails.target_drawn.as_organizer') }} {{ __('emails.target_drawn.do_not_forward') }}

    @include('emails/partials/organizer_plain')
@endif
@endsection
