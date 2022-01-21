@component('mail::message')

# Bonjour {{ $name }},

Voici un message de {{ $targetName }} :

> {{ $content }}

@endcomponent