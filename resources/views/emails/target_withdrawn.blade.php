@component('mail::message')

# @lang('Bonjour :name,', ['name' => $name])

@lang('Malheureusement, la personne à qui vous deviez faire un petit cadeau s\'est désistée...')

@lang('Mais pas d\'inquiétude, SecretSanta.fr vous a attribué une nouvelle cible !')

@lang('Vous allez devoir faire un cadeau à :target }}, maintenant.', ['target' => $targetName])

@lang('Si cette personne avait envoyé des messages à son santa, vous les recevrez prochainement.')

@endcomponent