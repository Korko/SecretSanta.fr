@extends('emails/layout_plain')

@section('title')
    Récapitulatif Organisateur
@endsection

@section('content')
    Bonjour {{ $organizerName }},

    Merci d'avoir organisé votre évènement en utilisant {{ config('app.name') }}.

    Afin de surveiller si tous les participants ont bien reçu leur email ou modifier une adresse en cas d'erreur, rendez vous sur votre interface personnalisée : {{ $panelLink }}

    Vous y trouverez aussi un moyen de récupérer un récapitulatif des participants ainsi que des exclusions que vous avez définies pour cet évènement. Ce fichier pourra être réutilisé sur {{ config('app.name') }} pour gagner du temps lors de l'organisation de votre prochain évènement.

    Amusez-vous bien !
@endsection