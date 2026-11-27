@extends('templates/kraftPage')

@section('title', __('home.footer.legal'))

@section('content')
        <section class="pg-card">
            <h2 class="pg-h2">Usage</h2>
            <p class="pg-p">Votre utilisation du site Web indique que vous avez lu et accepté nos pratiques de confidentialité, telles qu'énoncées dans cette politique. Si vous n'acceptez pas les modalités d'utilisation de cette politique, veuillez, s'il-vous-plaît, ne pas utiliser ce site Web. Le développeur se réserve le droit de modifier à tout moment cette politique s'il le juge nécessaire.</p>
            <p class="pg-p">Le site {{ config('app.url') }} collecte des informations personnelles comme le nom et l'adresse email des participants soumis par un visiteur. Alors organisateur d'évènement, vous reconnaissez avoir eu le consentement des autres participants pour partager leurs données personnelles dans cet outil. Ces données ne sont utilisées que pour la gestion de cet évènement organisé et ne sont partagées avec aucun partenaire.</p>
            <p class="pg-p">Le développeur se réserve le droit de supprimer à tout moment un évènement ainsi que toutes les données associées, notamment en cas d'abus évident de l'outil.</p>
        </section>
        <section class="pg-card">
            <h2 class="pg-h2">Engagement pour la sécurité</h2>
            <p class="pg-p">Le développeur s'engage à ce que la sécurité de vos données personnelles soient au coeur de la conception de cet outil et que le chiffrements des dites données soit utilisé autant que possible afin d'en garantir la protection même en cas de fuite.</p>
        </section>
        <section class="pg-card">
            <h2 class="pg-h2">Durées de stockage de vos données</h2>
            <p class="pg-p">Quelques jours après la date indiquée lors de la création de l'évènement, toutes les données associées sont supprimées.</p>
        </section>
        <section class="pg-card">
            <h2 class="pg-h2">Les droits que vous avez sur vos données</h2>
            <p class="pg-p">A tout moment, un organisateur d'évènement peut supprimer manuellement toutes les données associées au dit évènement depuis son interface personnalisée dont le lien a été reçu par email. Si vous jugez que l'organisateur de l'évènement n'avait pas obtenu votre consentement pour l'utilisation de vos données personnelles sur cet outil ou que vous souhaitez supprimer toutes vos données de ce site relatives à un évènement, merci de contacter le développeur en fournissant une copie d'un email reçu par le site. De part le chiffrement des données, le développeur ne saurait retrouver vos données sans le lien fourni dans cet email.</p>
        </section>
        <section class="pg-card">
            <h2 class="pg-h2">Licences</h2>
            <p class="pg-p">Le contenu du site {{ config('app.url') }} est sous <a class="pg-link pg-inline" href="https://tldrlegal.com/license/apache-license-2.0-(apache-2.0)">licence Apache 2.0</a>.</p>
            <p class="pg-p">Le code source est intégralement disponible sur <a class="pg-link pg-inline" href="https://framagit.org/Korko/SecretSanta">Framagit</a> et <a class="pg-link pg-inline" href="https://github.com/Korko/SecretSanta.fr/">GitHub</a>.</p>
        </section>
        <section class="pg-card">
            <h2 class="pg-h2">Informatique et Libertés</h2>
            <p class="pg-p">Aucun usage en dehors du bon fonctionnement de ce site ne saurait être fait de vos données personnelles. Celles-ci sont chiffrées afin de ne pas être utilisable sans votre action ou votre consentement directe.</p>
        </section>
        <section class="pg-card">
            <h2 class="pg-h2">Éditeur</h2>
            <p class="pg-p">Développeur :  Jérémy LEMESLE</p>
            <p class="pg-p">Hébergeur : OVH SAS – 2 rue Kellermann – BP 80157 – 59053 Roubaix Cedex 1 – Téléphone : 1007</p>
        </section>
        <section class="pg-card">
            <h2 class="pg-h2">Limitations de responsabilité</h2>
            <p class="pg-p">Le développeur ne pourra être tenu pour responsable des dommages directs et indirects causés au matériel de l’utilisateur, lors de l’accès au site.</p>
            <p class="pg-p">Le développeur décline toute responsabilité quant à l’utilisation qui pourrait être faite des informations et contenus présents sur le site.</p>
            <p class="pg-p">Le développeur s’engage à sécuriser au mieux le site, cependant sa responsabilité ne pourra être mise en cause si des données indésirables sont importées et installées sur son site à son insu.</p>
            <p class="pg-p">Des espaces interactifs (espace contact au père noël secret) sont à la disposition des participants. De part le chiffrement des données, le développeur ne saurait être responsable du contenu de ces échanges</p>
        </section>
        <section class="pg-card">
            <h2 class="pg-h2">Cookies</h2>
            <p class="pg-p">Un « cookie » est un fichier de petite taille qui enregistre des informations relatives à la navigation d’un utilisateur sur un site. Le site {{ config('app.url') }} n'utilise aucun cookie et se passe donc de demande d'autorisation pour ceux-ci.</p>
        </section>
@stop
