@extends('emails/layout_plain')

@section('title')
    Récapitulatif Organisateur
@endsection

@section('content')
    Bonjour {{ $organizerName }},

    Merci d'avoir organisé votre évènement en utilisant {{ config('app.name') }}.

@include('emails/partials/organizer_plain', ['organizer' => get_defined_vars()])
    Amusez-vous bien !
@endsection