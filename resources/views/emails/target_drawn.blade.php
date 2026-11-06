@extends('emails/layout')

@section('title')
    Message aux participants
@endsection

@section('content')
    <blockquote>{!! nl2br(htmlentities($content)) !!}</blockquote>

    <hr/>

    <p style="padding-bottom:10px !important">Vous pouvez écrire à votre Père Noël Secret ! Ca se passe ici : <a href="{{ $dearSantaLink }}">{{ $dearSantaLink }}</a></p>

    <p style="padding-bottom:10px !important"><small>Cet email ne vous est pas destiné, ou vous ne participez pas ? Signalez-le à l'organisateur en bas de cette même page.</small></p>
@if($organizer)

    <hr/>

    <p style="padding-bottom:10px !important"><strong>En tant qu'organisateur</strong> (ne transférez pas cet email, il contient votre lien d'administration) :</p>

    @include('emails/partials/organizer')
@endif
@endsection
