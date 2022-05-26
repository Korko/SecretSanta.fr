@component('mail::message')

# @lang('Bonjour :name,', ['name' => $name])

{{ $content }}

@lang('Pour répondre ou pour écrire vous même au/à la bénéficiaire de votre cadeau, vous pouvez vous rendre sur :link', ['link' => $dearSantaLink])

@endcomponent