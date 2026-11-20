@extends('emails/layout_plain')

@section('title')
    Confirmation de votre nouvelle adresse
@endsection

@section('content')
    Bonjour {{ $name }},
    Pour recevoir désormais les emails de votre évènement SecretSanta.fr à cette adresse, saisissez ce code sur votre page de participant(e) :
    {{ $code }}
    Ce code est valable une heure. Si vous n'êtes pas à l'origine de cette demande, ignorez simplement ce message : rien ne change.
@endsection
