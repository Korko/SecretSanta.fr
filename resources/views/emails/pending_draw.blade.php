@component('mail::message')

# @lang('Bonjour :name,', ['name' => $name])

@lang('Afin de pouvoir lancer le tirage au sort et que chaque participant reçoive sa cible, merci de confirmer votre adresse email en cliquant sur le bouton ci-dessous :')

@component('mail::button', ['url' => $validationLink])
@lang('Confirmer mon adresse email')
@endcomponent

@endcomponent
