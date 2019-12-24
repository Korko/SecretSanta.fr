<template>
    <form
        id="fetch"
        :action="fetchurl"
        @submit.prevent="submit"
        method="post"
        autocomplete="off"
    >
        <input type="hidden" name="_token" :value="csrf" />
        <input type="hidden" name="key" :value="key" />
        <timer :delay="2000">
            <button
                type="submit"
                class="btn btn-primary btn-lg"
                :disabled="loading"
            >
                <span v-if="loading">
                    <span class="fas fa-spinner"></span> Chargement en cours...
                </span>
                <span v-else>Charger</span>
            </button>
        </timer>
    </form>
</template>

<script>
    import { mapState } from 'vuex';

    import Timer from './timer.vue';

    import VueAjax from '../mixins/ajaxVue.js';

    export default {
        template: '#fetcher-template',
        mixins: [VueAjax],
        components: { Timer },
        props: ['fetchurl'],
        data: () => {
            return {
                loading: false
            };
        },
        computed: mapState(['csrf', 'key']),
        mounted() {
            this.$nextTick(function() {
                this.loading = true;
                this.submitForm('#fetch', {
                    success: json => {
                        this.$emit('success', json);
                    },
                    error: () => {
                        this.$emit('error');
                    }
                });
            });
        }
    };
</script>
