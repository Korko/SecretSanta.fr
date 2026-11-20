@extends('emails/layout')

@section('title')
    {{ __('emails.dear_santa.heading', ['name' => $targetName]) }}
@endsection

@section('content')
    <blockquote>{!! nl2br(htmlentities( $content )) !!}</blockquote>
@endsection
