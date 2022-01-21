@component('mail::message')

# Bonjour {{ $name }},

Vous êtes amené à participer à une remise de cadeaux et voici le message de votre organisateur :

> {{ $content }}

@component('mail::button', ['url' => $dearSantaLink])
Ecrire à mon Père Noël Secret
@endcomponent

@endcomponent