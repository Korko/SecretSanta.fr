@extends('emails/layout')

@section('title')
    Message de {{ $targetName }}
@endsection

@section('content')
    <blockquote>{!! nl2br(htmlentities( $content )) !!}</blockquote>
@endsection
