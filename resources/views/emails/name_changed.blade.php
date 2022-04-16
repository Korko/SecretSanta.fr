@component('mail::message')

# @lang('Bonjour :name,', ['name' => $name])

@lang('Votre organisateur/trice a décidé de renommer votre cible en :target.', ['target' => $targetName])

@endcomponent