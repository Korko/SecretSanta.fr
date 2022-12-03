<script>
    import AjaxForm from '@/Components/AjaxForm.vue';

    export default {
        components: {
            AjaxForm
        },

        props: {
            routes: {
                type: Object,
                required: true
            },
        },

        data: function() {
            return {
                reason: null,
                reasonTxt: null
            };
        },

        validations() {
            if (this.reason !== 'other') {
                return {
                    reason: {
                        required
                    }
                };
            } else {
                return {
                    reason: {
                        required
                    },
                    reasonTxt: {
                        required
                    }
                };
            }
        },
    };
</script>

<template>
    <div>
        <div v-cloak class="row text-center form">
            <ajax-form :action="routes.report" :button-send="$t('common.form.report')" send-icon="gavel">
                <template #default>
                    <label>Raison du signalement :</label>
                    <select v-model="reason" required>
                        <option value="spam">Ce tirage au sort est un spam</option>
                        <option value="unwanted">Je n'ai jamais demandé à participer à ce tirage au sort</option>
                        <option value="other">Autre raison</option>
                    </select>
                    <input v-if="reason === 'other'" v-model="reasonTxt" required />
                </template>
            </ajax-form>
        </div>
    </div>
</template>

<style scoped>

</style>
