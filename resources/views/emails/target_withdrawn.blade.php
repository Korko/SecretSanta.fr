@extends('emails/layout', ['title' => 'target_withdrawn'])

@section('content')
    <p style="padding-bottom:10px !important">Bonjour {{ $santaName }},</p>

    <p style="padding-bottom:10px !important">Malheureusement, la personne à qui vous deviez faire un petit cadeau s'est désistée...</p>

    <p style="padding-bottom:10px !important">Mais pas d'inquiétude, SecretSanta.fr t'a attribué une nouvelle cible !</p>

    <p style="padding-bottom:10px !important">Tu vas devoir faire un cadeau à {{ $targetName }}, maintenant.</p>

    <p style="padding-bottom:10px !important">Si cette personne avait envoyé des messages à son santa, tu les recevras prochainement.</p>
@endsection