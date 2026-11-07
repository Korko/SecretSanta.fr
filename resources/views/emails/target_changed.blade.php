@extends('emails/layout')

@section('title')
    Changement de cible
@endsection

@section('content')
    <p style="padding-bottom:10px !important">Bonjour {{ $santaName }},</p>

    <p style="padding-bottom:10px !important">La liste des participant(e)s de ton évènement a été modifiée, et il n'a pas été possible de l'ajuster sans changer ta cible.</p>

    <p style="padding-bottom:10px !important">Tu vas devoir faire un cadeau à {{ $targetName }}, maintenant.</p>

    <p style="padding-bottom:10px !important">Si cette personne avait envoyé des messages à son santa, tu les recevras prochainement.</p>
@endsection