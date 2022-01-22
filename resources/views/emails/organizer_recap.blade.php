@component('mail::message')

# {{ __('Bonjour :name,', ['name' => $name]) }},

{{ __('Afin de surveiller si tous les participants ont bien reçu leur email ou modifier une adresse en cas d\'erreur, rendez vous sur votre interface personnalisée.') }}

@component('mail::button', ['url' => $panelLink])
{{ __('Aller sur le panneau d\'organisateur') }}
@endcomponent

{{ __('Vous y trouverez aussi un moyen de récupérer un récapitulatif des participants ainsi que des exclusions que vous avez définies pour cet évènement. Ce fichier pourra être réutilisé sur :app pour gagner du temps lors de l\'organisation de votre prochain évènement.', ['app' => config('app.name')]) }}

{{ __('Amusez-vous bien !') }}

@endcomponent