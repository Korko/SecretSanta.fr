<script>
    import Layout from '@/Layouts/Main.vue';

    import NavLink from '@/Components/NavLink.vue';
    import NavPageLink from '@/Components/NavPageLink.vue';

    export default {
        components: {
            Layout,
            NavLink,
            NavPageLink
        }
    }
</script>

<template>
    <Layout>
        <template #navbar-right>
            <NavPageLink :href="route('form.index')">
                <i18n-t keypath="common.nav.go" />
            </NavPageLink>
            <NavPageLink :href="route('dashboard')">
                <i18n-t keypath="common.nav.dashboard" />
            </NavPageLink>
        </template>
        <template #content>
            <pre>
                # Usage

                Votre utilisation du site Web indique que vous avez lu et accepté nos pratiques de confidentialité, telles qu'énoncées dans cette politique.
                Si vous n'acceptez pas les modalités d'utilisation de cette politique, veuillez, s'il-vous-plaît, ne pas utiliser ce site Web.
                Le développeur se réserve le droit de modifier à tout moment cette politique s'il le juge nécessaire.

                Le site SecretSanta.fr collecte des informations personnelles comme le nom et l'adresse email des participants soumis par un visiteur.
                Alors organisateur d'évènement, vous reconnaissez avoir eu le consentement des autres participants pour partager leurs données personnelles dans cet outil.
                Ces données ne sont utilisées que pour la gestion de cet évènement organisé et ne sont partagées avec aucun partenaire.

                Vous vous engagez à ne pas utiliser SecretSanta.fr pour diffuser du contenu commercial non solicité (spam), des messages harcelants ou à caractères abusifs, diffamatoires, obsène ou tout autre contenu illégal.
                Vous vous engagez également à ne pas utiliser SecretSanta.fr en vous faisant passer pour une autre personne, pour procéder à du hameçonnage (phishing), ou autre escroquerie.

                Le développeur se réserve le droit de supprimer à tout moment un évènement ainsi que toutes les données associées, notamment en cas d'abus évident de l'outil.

                # Engagement pour la sécurité

                Le développeur s'engage à ce que la sécurité de vos données personnelles soient au coeur de la conception de cet outil
                et que le chiffrements des dites données soit utilisé autant que possible afin d'en garantir la protection même en cas de fuite.

                # Durées de stockage de vos données

                Quelques jours après la date indiquée lors de la création de l'évènement, toutes les données associées sont supprimées.

                # Les droits que vous avez sur vos données

                A tout moment, un organisateur d'évènement peut supprimer manuellement toutes les données associées au dit évènement depuis son interface personnalisée dont le lien a été reçu par email.
                Si vous jugez que l'organisateur de l'évènement n'avait pas obtenu votre consentement pour l'utilisation de vos données personnelles sur cet outil
                ou que vous souhaitez supprimer toutes vos données de ce site relatives à un évènement, merci de contacter le développeur en fournissant une copie d'un email reçu par le site.
                De part le chiffrement des données, le développeur ne saurait retrouver vos données sans le lien fourni dans cet email.

                # Licences

                Le contenu du site SecretSanta.fr est sous licence Apache 2.0 (https://tldrlegal.com/license/apache-license-2.0-(apache-2.0)).

                Le code source est intégralement disponible sur les sites https://framagit.org/Korko/SecretSanta et https://github.com/Korko/SecretSanta.fr/.

                # Informatique et Libertés

                Aucun usage en dehors du bon fonctionnement de ce site ne saurait être fait de vos données personnelles.
                Celles-ci sont chiffrées afin de ne pas être utilisable sans votre action ou votre consentement directe.

                # Éditeur

                Développeur :  Jérémy LEMESLE

                Hébergeur : OVH SAS – 2 rue Kellermann – BP 80157 – 59053 Roubaix Cedex 1 – Téléphone : 1007

                # Limitations de responsabilité.

                Le développeur ne pourra être tenu pour responsable des dommages directs et indirects causés au matériel de l’utilisateur, lors de l’accès au site.

                Le développeur décline toute responsabilité quant à l’utilisation qui pourrait être faite des informations et contenus présents sur le site.

                Le développeur s’engage à sécuriser au mieux le site, cependant sa responsabilité ne pourra être mise en cause si des données indésirables sont importées et installées sur son site à son insu.

                Des espaces interactifs (espace contact au père noël secret) sont à la disposition des participants.
                De part le chiffrement des données, le développeur ne saurait être responsable du contenu de ces échanges

                # Cookies

                Un « cookie » est un fichier de petite taille qui enregistre des informations relatives à la navigation d’un utilisateur sur un site.
                Le site SecretSanta.fr n'utilise aucun cookie et se passe donc de demande d'autorisation pour ceux-ci.

                # Protection

                Afin de se prévenir de potentiels abus, l'entête suivant sera ajouté dans les emails que vous enverrez via SecretSanta.fr : X-Originating-IP:
            </pre>
        </template>
    </Layout>
</template>

<style scoped>

</style>
