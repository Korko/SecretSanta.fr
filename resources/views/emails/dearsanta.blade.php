@extends('emails/tracked_layout')

@yield('title', "Message de {{ $targetName }}")

@section('content')
    <blockquote>{!! nl2br(htmlentities( $content )) !!}</blockquote>
@endsection
