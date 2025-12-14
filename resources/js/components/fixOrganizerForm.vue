<script>
    import DefaultForm from './form.vue';

    export default {
        extends: DefaultForm,
        props: {
            action: {
                type: String,
                required: true
            }
        },
        data() {
            return {
                url: '',
                email: ''
            };
        },
        methods: {
            success(response) {
                this.$dialog.alert(response.message);
                console.debug(response);
            },
            reset() {
                this.url = '';
                this.email = '';
            }
        }
    };
</script>

<template>
    <ajax-form :action="action" @success="success" @reset="reset" :autoReset="true" :button-send="$t('organizer.fix')" send-icon="wrench">
        <template #default="{ fieldError }">
            <div class="form-group">
                <label for="url" class="col-form-label">{{ $t('organizer.url') }}</label>
                <input name="url" type="url" class="form-control" v-model.trim="url" placeholder="https://secretsanta.fr/dearSanta/..." />
            </div>
            <div class="form-group">
                <label for="email" class="col-form-label">{{ $t('form.organizer.email') }}</label>
                <input name="email" type="email" class="form-control" v-model.trim="email" placeholder="moi@gmail.com" />
            </div>
        </template>
    </ajax-form>
</template>

<style scoped>
    @import "vuejs-dialog/dist/vuejs-dialog.min.css";
</style>
