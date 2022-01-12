@extends('emails/layout', ['title' => 'confirm_withdrawal'])

@section('content')
    <p style="padding-bottom:10px !important">Bonjour {{ $santaName }},</p>

    <p style="padding-bottom:10px !important">Votre demande de retrait du SecretSanta de {{ $organizerName }} a bien été prise en compte.</p>

    <p style="padding-bottom:10px !important">Votre ancien santa s'est trouvé une nouvelle cible et toutes vos données personnelles ont été supprimées du site SecretSanta.fr.</p>
@endsection