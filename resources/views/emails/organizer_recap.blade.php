@component('mail::message')

# @lang('Bonjour :name,', ['name' => $name])

@lang('Afin de surveiller si tous les participants ont bien reçu leur email ou modifier une adresse en cas d\'erreur, rendez vous sur votre interface personnalisée.')

@component('mail::button', ['url' => $panelLink])
@lang('Aller sur le panneau d\'organisateur')
@endcomponent

@lang('Vous y trouverez aussi un moyen de récupérer un récapitulatif des participants ainsi que des exclusions que vous avez définies pour cet évènement. Ce fichier pourra être réutilisé sur :app pour gagner du temps lors de l\'organisation de votre prochain évènement.', ['app' => config('app.name')])

@lang('Amusez-vous bien !')

@endcomponent