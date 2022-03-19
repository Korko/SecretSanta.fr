@component('mail::message')

# {{ __('Bonjour :name,', ['name' => $name]) }}

{{ __('Vous êtes amené à participer à une remise de cadeaux.') }}

{{ __('Voici le message de votre organisateur :') }}
@component('mail::quote')
{{ $content }}
@endcomponent

{{ __('Le tirage au sort vous a attribué comme bénéficiaire :target. Cette personne n\'est pas au courant que vous avez tiré son nom au hasard.') }}

{{ __('De l\'autre côté, quelqu\'un a aussi tiré votre nom au hasard mais personne ne sait qui. Vous pouvez quand même lui écrire si besoin (pour se présenter, donner des idées cadeaux, son adresse postale si besoin, etc.).') }}

@component('mail::button', ['url' => $dearSantaLink])
{{ __('Ecrire à mon Père Noël Secret') }}
@endcomponent

@endcomponent