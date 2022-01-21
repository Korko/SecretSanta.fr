@component('mail::message')

# Bonjour {{ $name }},

Malheureusement, la personne à qui vous deviez faire un petit cadeau s'est désistée...

Mais pas d'inquiétude, SecretSanta.fr vous a attribué une nouvelle cible !

Vous allez devoir faire un cadeau à {{ $targetName }}, maintenant.

Si cette personne avait envoyé des messages à son santa, vous les recevrez prochainement.

@endcomponent