    <p style="padding-bottom:10px !important">Afin de surveiller si tous les participants ont bien reçu leur email ou modifier une adresse en cas d'erreur, rendez vous sur votre interface personnalisée : <a href="{{ $organizer['panelLink'] }}">{{ $organizer['panelLink'] }}</a></p>

    <p style="padding-bottom:10px !important">Vous y trouverez aussi un moyen de récupérer un récapitulatif des participants ainsi que des exclusions que vous avez définies pour cet évènement. Ce fichier pourra être réutilisé sur {{ config('app.name') }} pour gagner du temps lors de l'organisation de votre prochain évènement.</p>

    @if($organizer['nextSolvable'])<p style="padding-bottom:10px !important">Dès que vous aurez révélé les cibles, ou à compter du {{ $organizer['expirationDate'] }}, un second récapitulatif sera disponible contenant la cible tirée au hasard par chaque participant dans leur liste d'exclusions respectives. Améliorant encore plus l'expérience pour le prochain évènement.</p>@endif

    <p style="padding-bottom:10px !important">Toutes les informations relatives à cet évènement seront supprimées le {{ $organizer['deletionDate'] }}. Cette interface ne sera alors plus disponible.</p>

