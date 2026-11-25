@extends('emails/layout')

@section('title')
    {{ __('emails.gift_suggestion.heading', ['name' => $targetName]) }}
@endsection

@section('content')
    <p style="padding-bottom:10px !important">{{ __('emails.gift_suggestion.from_anonymous', ['name' => $targetName]) }}</p>

    <blockquote>{!! nl2br(htmlentities( $content )) !!}</blockquote>

    <p style="padding-bottom:10px !important">{!! __('emails.gift_suggestion.secret', ['name' => e($targetName), 'link' => '<a href="'.e($dearSantaLink).'">'.e($dearSantaLink).'</a>']) !!}</p>
@endsection
