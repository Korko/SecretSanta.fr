<script>
    import fetch from '../partials/fetch.js';

    import Timer from './timer.vue';

    export default {
        components: { Timer },
        props: {
            fetchUrl: {
                type: String,
                required: true
            }
        },
        data: () => ({
            loading: false
        }),
        mounted() {
            this.loading = true;
            fetch(this.fetchUrl)
                .then(response => {
                    this.$emit('success', response);
                })
                .catch(response => {
                    this.$emit('error');
                })
                .finally(() => {
                    this.loading = false;
                });
        }
    };
</script>

<template>
    <timer :delay="2000">
        <!-- TODO Erreur -->
    </timer>
</template>
