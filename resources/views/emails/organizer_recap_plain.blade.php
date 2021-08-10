@extends('emails/layout_plain')

@section('title')
    Récapitulatif Organisateur
@endsection

@section('content')
    Bonjour {{ $organizerName }},

    Merci d'avoir organisé votre évènement en utilisant {{ config('app.name') }}.

    Afin de surveiller si tous les participants ont bien reçu leur email ou modifier une adresse en cas d'erreur, rendez vous sur votre interface personnalisée : {{ $panelLink }}

    Cette interface ne sera plus disponible à compter du {{ $expirationDate }}, à cette date vous recevrez un email contenant un fichier récapitulatif des participants ainsi que des exclusions que vous avez définies pour cet évènement. Ce fichier pourra être réutilisé sur SecretSanta.fr pour gagner du temps lors de l'organisation de votre prochain évènement.

    Amusez-vous bien !
@endsection