@extends('emails/layout')

@section('title')
    Récapitulatif Organisateur
@endsection

@section('content')
    <p style="padding-bottom:10px !important">Bonjour {{ $organizerName }},</p>

    <p style="padding-bottom:10px !important">Merci d'avoir organisé votre évènement en utilisant <a href="{{ config('app.url') }}">{{ config('app.name') }}</a>.</p>

    <p style="padding-bottom:10px !important">Afin de surveiller si tous les participants ont bien reçu leur email ou modifier une adresse en cas d'erreur, rendez vous sur votre interface personnalisée : <a href="{{ $panelLink }}">{{ $panelLink }}</a>

    <p style="padding-bottom:10px !important">Vous y trouverez aussi un moyen de récupérer un récapitulatif des participants ainsi que des exclusions que vous avez définies pour cet évènement. Ce fichier pourra être réutilisé sur SecretSanta.fr pour gagner du temps lors de l'organisation de votre prochain évènement.</p>

    @if($nextSolvable)<p style="padding-bottom:10px !important">À compter du {{ $expirationDate }}, un second récapitulatif sera disponible contenant parmi les exclusions de chaque participant, la cible tirée au hasard cette fois-ci. Améliorant encore plus l'expérience pour le prochain évènement.</p>@endif

    <p style="padding-bottom:10px !important">Toutes les informations relatives à cet évènement seront supprimées le {{ $deletionDate }}. Cette interface ne sera alors plus disponible.</p>

    <p style="padding-bottom:10px !important">Amusez-vous bien !</p>
@endsection
