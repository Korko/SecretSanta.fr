<script>
    import axios from '../partials/axios.js';

    import Timer from './timer.vue';

    export default {
        components: { Timer },
        props: {
            fetchurl: {
                type: String,
                required: true
            }
        },
        data: () => {
            return {
                loading: false
            };
        },
        mounted() {
            this.loading = true;
            axios
                .get(this.fetchurl)
                .then(response => {
                    this.$emit('success', response.data);
                })
                .catch(error => {
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
