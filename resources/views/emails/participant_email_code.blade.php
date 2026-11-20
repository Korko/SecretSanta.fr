@extends('emails/layout')

@section('title')
    Confirmation de votre nouvelle adresse
@endsection

@section('content')
    <p style="padding-bottom:10px !important">Bonjour {{ $name }},</p>

    <p style="padding-bottom:10px !important">Pour recevoir désormais les emails de votre évènement SecretSanta.fr à cette adresse, saisissez ce code sur votre page de participant(e) :</p>

    <p style="padding-bottom:10px !important;font-size:24px;font-weight:bold;letter-spacing:4px">{{ $code }}</p>

    <p style="padding-bottom:10px !important">Ce code est valable une heure. Si vous n'êtes pas à l'origine de cette demande, ignorez simplement ce message : rien ne change.</p>
@endsection
