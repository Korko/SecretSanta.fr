<script>
    import useVuelidate from '@vuelidate/core';


    import { required, email } from '@vuelidate/validators'

    import alertify from '../partials/alertify.js';
    import DefaultForm from './form.vue';

    export default {
        setup: () => ({ v$: useVuelidate() }),
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
                alertify.confirmAlert(response.message);
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
    <ajax-form :action="action" :validator="v$" @success="success" @reset="reset" :autoReset="true" :button-send="$t('organizer.fix')" send-icon="wrench">
        <template #default="{ fieldError }">
            <div class="form-group">
                <label for="url" class="col-form-label">{{ $t('organizer.url') }}</label>
                <input name="url" type="url" class="form-control" :class="{'is-invalid': v$.url.$error || fieldError('url')}" v-model.trim="v$.url.$model" placeholder="https://secretsanta.fr/dearSanta/..." />
                <div class="invalid-feedback" v-if="v$.url.$error || fieldError('url')">
                    <div v-if="v$.url.required.$invalid">{{ $t('validation.custom.fixOrganizer.url.required') }}</div>
                    <div v-else-if="v$.url.format.$invalid">{{ $t('validation.custom.fixOrganizer.url.format') }}</div>
                    <div v-else-if="fieldError('url')">{{ fieldError('url') }}</div>
                </div>
            </div>
            <div class="form-group">
                <label for="email" class="col-form-label">{{ $t('form.organizer.email') }}</label>
                <input name="email" type="email" class="form-control" :class="{'is-invalid': v$.email.$error || fieldError('email')}" v-model.trim="v$.email.$model" placeholder="moi@gmail.com" />
                <div class="invalid-feedback" v-if="v$.email.$error || fieldError('email')">
                    <div v-if="v$.email.required.$invalid">{{ $t('validation.custom.fixOrganizer.email.required') }}</div>
                    <div v-else-if="v$.email.format.$invalid">{{ $t('validation.custom.fixOrganizer.email.format') }}</div>
                    <div v-else-if="fieldError('email')">{{ fieldError('email') }}</div>
                </div>
            </div>
        </template>
    </ajax-form>
</template>

