@extends('emails/layout_plain')

@section('title')
    Changement d'adresse organisateur
@endsection

@section('content')
    Bonjour {{ $organizerName }},
    L'adresse email organisateur de votre évènement SecretSanta.fr vient d'être remplacée par {{ $newEmail }} depuis la page de correction d'adresse.
    Si c'est bien vous, vous pouvez ignorer ce message.
    Sinon, le lien vers votre panneau organisateur reçu précédemment fonctionne toujours : ouvrez-le et remettez votre adresse.
@endsection
