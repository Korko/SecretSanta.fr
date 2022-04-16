@component('mail::message')

# @lang('Bonjour :name,', ['name' => $name])

@lang('Voici un message de :target :', ['target' => $targetName])

> {{ $content }}

@endcomponent