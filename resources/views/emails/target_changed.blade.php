@extends('emails/layout')

@section('title')
    Changement de cible
@endsection

@section('content')
    <p style="padding-bottom:10px !important">Bonjour {{ $santaName }},</p>

    <p style="padding-bottom:10px !important">Malheureusement, un participant de votre évènement s'est désisté...</p>

    <p style="padding-bottom:10px !important">De plus, aucune résolution simple n'était possible et SecretSanta.fr a  été contraint de t'attribuer une nouvelle cible...</p>

    <p style="padding-bottom:10px !important">Tu vas devoir faire un cadeau à {{ $targetName }}, maintenant.</p>

    <p style="padding-bottom:10px !important">Si cette personne avait envoyé des messages à son santa, tu les recevras prochainement.</p>
@endsection