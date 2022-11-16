<script>
    import fetch from '@/Modules/fetch.js';
    import Echo from '@/Modules/echo.js';

    export default {
        props: {
            routes: {
                type: Object
            },
            pending: {
                type: Object
            }
        },
        created() {
            // TODO test
            Echo.channel('pending_draw.'+this.pending.id)
                .listen('.status.update', data => {
                    console.debug('echo', data);
                    this.pending = {...this.pending, ...data};
                });
        },
        methods: {
            process() {
                this.pending.status = 'drawing';
                return fetch(this.routes.process)
                    .then(response => {
                        // The actual status is 'started' but keep that message for landing, not confirmation
                        this.pending.status = 'drawn';

                        // response.message for a confirmation text
                    })
                    .catch(response => {
                        this.pending.status = 'waiting';
                    });
            }
        }
    }
</script>

<template>
    Bonjour {{ pending.organizer_name }},
    <div v-if="pending.status === 'created'">
        Votre évènement SecretSanta est prêt à démarrer.
        <button @click="process">Lancer le tirage au sort</button>
    </div>
    <div v-else-if="pending.status === 'drawing'">
        Le tirage au sort est en cours
        <button disabled>Veuillez patienter</button>
    </div>
    <div v-else-if="pending.status === 'started'">
        Votre évènement SecretSanta est déjà en cours
    </div>
    <div v-else-if="pending.status === 'drawn'">
        Votre évènement SecretSanta a bien été démarré
    </div>
</template>