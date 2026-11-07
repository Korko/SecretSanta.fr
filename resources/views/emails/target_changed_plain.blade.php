@extends('emails/layout_plain')

@section('title')
    Changement de cible
@endsection

@section('content')
    Bonjour {{ $santaName }},
    La liste des participant(e)s de ton évènement a été modifiée, et il n'a pas été possible de l'ajuster sans changer ta cible.
    Tu vas devoir faire un cadeau à {{ $targetName }}, maintenant.
    Si cette personne avait envoyé des messages à son santa, tu les recevras prochainement.
@endsection