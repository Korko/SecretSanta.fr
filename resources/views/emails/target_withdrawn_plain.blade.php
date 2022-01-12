@extends('emails/layout_plain', ['title' => 'target_withdrawn'])

@section('content')
    Bonjour {{ $santaName }},
    Malheureusement, la personne à qui vous deviez faire un petit cadeau s'est désistée...
    Mais pas d'inquiétude, SecretSanta.fr t'a attribué une nouvelle cible !
    Tu vas devoir faire un cadeau à {{ $targetName }}, maintenant.
    Si cette personne avait envoyé des messages à son santa, tu les recevras prochainement.
@endsection