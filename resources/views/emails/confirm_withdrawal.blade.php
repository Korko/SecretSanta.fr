@component('mail::message')

# Bonjour {{ $name }},

Votre demande de retrait du SecretSanta de {{ $organizerName }} a bien été prise en compte.

Votre ancien santa s'est trouvé une nouvelle cible et toutes vos données personnelles ont été supprimées du site {{ config('app.name') }}.

@endcomponent