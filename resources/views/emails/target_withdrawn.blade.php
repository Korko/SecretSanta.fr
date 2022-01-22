@component('mail::message')

# {{ __('Bonjour :name,', ['name' => $name]) }}

{{ __('Malheureusement, la personne à qui vous deviez faire un petit cadeau s\'est désistée...') }}

{{ __('Mais pas d\'inquiétude, SecretSanta.fr vous a attribué une nouvelle cible !') }}

{{ __('Vous allez devoir faire un cadeau à :target }}, maintenant.', ['target' => $targetName]) }}

{{ __('Si cette personne avait envoyé des messages à son santa, vous les recevrez prochainement.') }}

@endcomponent