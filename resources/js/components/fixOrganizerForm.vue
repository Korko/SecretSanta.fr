<script>
    import Vue from 'vue';

    import VuejsDialog from 'vuejs-dialog';
    Vue.use(VuejsDialog);

    import Vuelidate from 'vuelidate';
    import { required, email } from 'vuelidate/lib/validators'
    Vue.use(Vuelidate);

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
        validations: {
            url: {
                required,
                format(value) {
                    // return /^(https?:\/\/)?([\da-z.-]+)\.([a-z.]{2,6})([/\w .-]*)*\/?(\?[;&a-z\d%_.~+=-]*)?#?([\w .-=]*)*\/?$/.test(value);
                    return /^(https?:\/\/)?([\da-z.-]+)\.([a-z.]{2,})\/dearSanta\/[0-9a-zA-Z]{5}\?signature=[a-f\d]+#[A-Za-z0-9.-=+]+$/.test(value);
                }
            },
            email: {
                required,
                format: email
            }
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
    <ajax-form :action="action" :$v="$v" @success="success" @reset="reset" :autoReset="true" :button-send="$t('organizer.fix')" send-icon="wrench">
        <template #default="{ fieldError }">
            <div class="form-group">
                <label for="url" class="col-form-label">{{ $t('organizer.url') }}</label>
                <input name="url" type="url" class="form-control" :class="{'is-invalid': $v.url.$error || fieldError('url')}" v-model.trim="$v.url.$model" placeholder="https://secretsanta.fr/dearSanta/..." />
                <div class="invalid-feedback" v-if="$v.url.$error || fieldError('url')">
                    <div v-if="!$v.url.required">{{ $t('validation.custom.fixOrganizer.url.required') }}</div>
                    <div v-else-if="!$v.url.format">{{ $t('validation.custom.fixOrganizer.url.format') }}</div>
                    <div v-else-if="fieldError('url')">{{ fieldError('url') }}</div>
                </div>
            </div>
            <div class="form-group">
                <label for="email" class="col-form-label">{{ $t('form.organizer.email') }}</label>
                <input name="email" type="email" class="form-control" :class="{'is-invalid': $v.email.$error || fieldError('email')}" v-model.trim="$v.email.$model" placeholder="moi@gmail.com" />
                <div class="invalid-feedback" v-if="$v.email.$error || fieldError('email')">
                    <div v-if="!$v.email.required">{{ $t('validation.custom.fixOrganizer.email.required') }}</div>
                    <div v-else-if="!$v.email.format">{{ $t('validation.custom.fixOrganizer.email.format') }}</div>
                    <div v-else-if="fieldError('email')">{{ fieldError('email') }}</div>
                </div>
            </div>
        </template>
    </ajax-form>
</template>

<style scoped>
    @import "~vuejs-dialog/dist/vuejs-dialog.min.css";
</style>
