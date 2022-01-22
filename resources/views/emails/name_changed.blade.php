@component('mail::message')

# {{ __('Bonjour :name,', ['name' => $name]) }}

{{ __('Votre organisateur/trice a décidé de renommer votre cible en :target.', ['target' => $targetName]) }}

@endcomponent