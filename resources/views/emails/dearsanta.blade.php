@component('mail::message')

# {{ __('Bonjour :name,', ['name' => $name]) }}

{{ __('Voici un message de :target :', ['target' => $targetName]) }}

> {{ $content }}

@endcomponent