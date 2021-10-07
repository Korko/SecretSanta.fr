@extends('emails/tracked_layout')

@section('title')
    Message aux participants
@endsection

@section('content')
    <blockquote>{!! nl2br(htmlentities($content)) !!}</blockquote>

    <hr/>

    <p style="padding-bottom:10px !important">Vous pouvez écrire à votre Père Noël Secret ! Ca se passe ici : <a href="{{ $dearSantaLink }}">{{ $dearSantaLink }}</a></p>
@endsection
