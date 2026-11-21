@extends('emails/layout')

@section('title')
    Désistement d'un(e) participant(e)
@endsection

@section('content')
    <p style="padding-bottom:10px !important">Bonjour {{ $organizerName }},</p>

    <p style="padding-bottom:10px !important">{{ $name }} s'est désisté(e) de votre évènement SecretSanta.fr depuis sa page de participant(e).</p>

    <p style="padding-bottom:10px !important">Son père/mère noël secret a reçu une nouvelle cible par email, personne d'autre n'a besoin de changer de cadeau. {{ $name }} n'apparaît plus sur votre panneau organisateur.</p>
@endsection
