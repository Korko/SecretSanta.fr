<script>
    import { mapState } from 'vuex';

    import Timer from './timer.vue';

    import AjaxForm from './ajaxForm.vue';

    export default {
        components: { Timer, AjaxForm },
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
        computed: mapState(['lang']),
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

<template>
    <ajax-form id="fetch" ref="form" :action="fetchurl">
        <timer :delay="2000">
            <button
                type="submit"
                class="btn btn-primary btn-lg"
                :disabled="loading"
            >
                <span v-if="loading">
                    <span class="fas fa-spinner" /> Chargement en cours...
                </span>
                <span v-else>Charger</span>
            </button>
        </timer>
    </ajax-form>
</template>
