@extends('emails/layout_plain')

@section('title')
    Changement de cible
@endsection

@section('content')
    Bonjour {{ $santaName }},
    Malheureusement, un participant de votre évènement s'est désisté...
    De plus, aucune résolution simple n'était possible et SecretSanta.fr a  été contraint de t'attribuer une nouvelle cible...
    Tu vas devoir faire un cadeau à {{ $targetName }}, maintenant.
    Si cette personne avait envoyé des messages à son santa, tu les recevras prochainement.
@endsection