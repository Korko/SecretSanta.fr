@extends('emails/layout', ['title' => 'name_changed'])

@section('content')
    <p style="padding-bottom:10px !important">Bonjour {{ $santaName }},</p>

    <p style="padding-bottom:10px !important">Votre organisateur/trice a décidé de renommer votre cible en {{ $targetName }}.</p>
@endsection