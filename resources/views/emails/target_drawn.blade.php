@extends('emails/layout')

@section('title')
    {{ __('emails.target_drawn.heading') }}
@endsection

@section('content')
    <blockquote>{!! nl2br(htmlentities($content)) !!}</blockquote>

    <hr/>

    <p style="padding-bottom:10px !important">{!! __('emails.target_drawn.write', ['link' => '<a href="'.e($dearSantaLink).'">'.e($dearSantaLink).'</a>']) !!}</p>

    <p style="padding-bottom:10px !important"><small>{{ __('emails.target_drawn.report') }}</small></p>
@if($organizer)

    <hr/>

    <p style="padding-bottom:10px !important"><strong>{{ __('emails.target_drawn.as_organizer') }}</strong> {{ __('emails.target_drawn.do_not_forward') }}</p>

    @include('emails/partials/organizer')
@endif
@endsection
