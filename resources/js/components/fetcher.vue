<template>
    <ajax-form
        id="fetch"
        :action="fetchurl"
        ref="form"
    >
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
    </ajax-form>
</template>

<script>
    import { mapState } from 'vuex';

    import Timer from './timer.vue';

    import AjaxForm from './ajaxForm.vue';

    export default {
        template: '#fetcher-template',
        components: { Timer, AjaxForm },
        props: ['fetchurl'],
        data: () => {
            return {
                loading: false
            };
        },
        mounted() {
            this.$nextTick(() => {
                this.loading = true;
                this.$refs.form.submit(undefined, {
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
