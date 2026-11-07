@extends('emails/layout')

@section('title')
    Changement d'adresse organisateur
@endsection

@section('content')
    <p style="padding-bottom:10px !important">Bonjour {{ $organizerName }},</p>

    <p style="padding-bottom:10px !important">L'adresse email organisateur de votre évènement SecretSanta.fr vient d'être remplacée par {{ $newEmail }} depuis la page de correction d'adresse.</p>

    <p style="padding-bottom:10px !important">Si c'est bien vous, vous pouvez ignorer ce message.</p>

    <p style="padding-bottom:10px !important">Sinon, le lien vers votre panneau organisateur reçu précédemment fonctionne toujours : ouvrez-le et remettez votre adresse.</p>
@endsection
