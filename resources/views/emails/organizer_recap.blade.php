@extends('emails/layout')

@section('title')
    Récapitulatif Organisateur
@endsection

@section('content')
    <p style="padding-bottom:10px !important">Bonjour {{ $organizerName }},</p>

    <p style="padding-bottom:10px !important">Merci d'avoir organisé votre évènement en utilisant <a href="{{ config('app.url') }}">{{ config('app.name') }}</a>.</p>

    @include('emails/partials/organizer', ['organizer' => get_defined_vars()])

    <p style="padding-bottom:10px !important">Amusez-vous bien !</p>
@endsection
