@component('mail::message')

# @lang('Bonjour :name,', ['name' => $name])

@lang('Votre demande de retrait du SecretSanta de :organizer a bien été prise en compte.', ['organizer' => $organizerName])

@lang('Votre ancien santa s\'est trouvé une nouvelle cible et toutes vos données personnelles ont été supprimées du site :app.', ['app' => config('app.name')])

@endcomponent