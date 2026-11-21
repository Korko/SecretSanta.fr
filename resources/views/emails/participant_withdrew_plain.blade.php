@extends('emails/layout_plain')

@section('title')
    Désistement d'un(e) participant(e)
@endsection

@section('content')
    Bonjour {{ $organizerName }},
    {{ $name }} s'est désisté(e) de votre évènement SecretSanta.fr depuis sa page de participant(e).
    Son père/mère noël secret a reçu une nouvelle cible par email, personne d'autre n'a besoin de changer de cadeau. {{ $name }} n'apparaît plus sur votre panneau organisateur.
@endsection
