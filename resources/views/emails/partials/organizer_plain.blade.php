    Afin de surveiller si tous les participants ont bien reçu leur email ou modifier une adresse en cas d'erreur, rendez vous sur votre interface personnalisée : {{ $organizer['panelLink'] }}

    Vous y trouverez aussi un récapitulatif des participants et des exclusions (joint à cet email), réutilisable sur {{ config('app.name') }} pour gagner du temps lors de l'organisation de votre prochain évènement.
@if($organizer['nextSolvable'])

    Dès que vous aurez révélé les cibles, ou à compter du {{ $organizer['expirationDate'] }}, un second récapitulatif sera disponible contenant la cible tirée par chaque participant.
@endif

    Toutes les informations relatives à cet évènement seront supprimées le {{ $organizer['deletionDate'] }}. Cette interface ne sera alors plus disponible.

