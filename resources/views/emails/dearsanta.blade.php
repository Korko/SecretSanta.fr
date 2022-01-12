@extends('emails/layout', ['title' => 'dear_santa'])

@section('content')
    <p style="padding-bottom:10px !important">Message de {{ $targetName }}</p>

    <blockquote>{!! nl2br(htmlentities( $content )) !!}</blockquote>
@endsection
