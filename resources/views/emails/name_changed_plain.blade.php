@extends('emails/layout_plain', ['title' => 'name_changed'])

@section('content')
    Bonjour {{ $santaName }},
    Votre organisateur/trice a décidé de renommer votre cible en {{ $targetName }}.
@endsection