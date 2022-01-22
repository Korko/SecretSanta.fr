@component('mail::message')

# {{ __('Bonjour :name,', ['name' => $name]) }}

{{ __('Vous êtes amené à participer à une remise de cadeaux et voici le message de votre organisateur :') }}

@component('mail::quote')
{{ $content }}
@endcomponent

@component('mail::button', ['url' => $dearSantaLink])
{{ __('Ecrire à mon Père Noël Secret') }}
@endcomponent

@endcomponent