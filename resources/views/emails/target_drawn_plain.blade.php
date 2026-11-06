@extends('emails/layout_plain')

@section('title')
    Message aux participants
@endsection

@section('content')
    > {{ $content }}

    ---

    Vous pouvez écrire à votre Père Noël Secret ! Ca se passe ici : {{ $dearSantaLink }}

    Cet email ne vous est pas destiné, ou vous ne participez pas ? Signalez-le à l'organisateur en bas de cette même page.
@if($organizer)

    ---

    En tant qu'organisateur (ne transférez pas cet email, il contient votre lien d'administration) :

    @include('emails/partials/organizer_plain')
@endif
@endsection