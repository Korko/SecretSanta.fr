<script>
    import Vue from 'vue';

    import Vuelidate from 'vuelidate';
    import { required } from 'vuelidate/lib/validators'
    Vue.use(Vuelidate);

    import axios from '../partials/axios.js';
    import store from '../partials/store.js';

    import DefaultForm from './form.vue';

    export default {
        extends: DefaultForm,
        props: {
            data: {
                type: Object,
                default() { return {}; }
            },
            routes: {
                type: Object,
                required: true
            }
        },
        data() {
            return {
                ...store,
                content: ''
            };
        },
        computed: {
            emails() {
                return Object.values(this.data.emails)
                    .map(email => Object.assign(email, email.mail))
                    .sort((email1, email2) => (new Date(email1.created_at) > new Date(email2.created_at) ? -1 : 1))
                    .map(email => {
                        email.created_at = new Date(email.created_at).toLocaleString('fr-FR');
                        return email;
                    });
            },
            checkUpdates() {
                return !!Object.values(this.data.emails).find(email => email.mail.delivery_status === 'created');
            }
        },
        created() {
            setInterval(() => {
                if (this.checkUpdates) this.fetchState();
            }, 1000);
        },
        validations: {
            content: {
                required
            }
        },
        methods: {
            success(data) {
                this.$set(this.data.emails, data.email.id, data.email);
            },
            fetchState() {
                return axios
                    .get(this.routes.fetchStateUrl)
                    .then(response => {
                        if (response.data.emails) {
                            Object.values(response.data.emails).forEach(email => {
                                var new_update = new Date(email.mail.updated_at);
                                var old_update = new Date(this.data.emails[email.id].mail.updated_at);
                                this.data.emails[email.id].mail.delivery_status =
                                    new_update > old_update
                                        ? email.mail.delivery_status
                                        : this.data.emails[email.id].mail.delivery_status;
                            });
                        }
                    });
            }
        }
    };
</script>

<template>
    <div>
        <ajax-form :action="routes.contactUrl" :$v="$v" @success="success">
            <fieldset>
                <div class="form-group">
                    <label for="mailContent">{{ $t('dearsanta.content.label') }}</label>
                    <div class="input-group">
                        <textarea
                            id="mailContent"
                            v-model="content"
                            name="content"
                            :placeholder="$t('dearsanta.content.placeholder')"
                            :class="{ 'form-control': true, 'is-invalid': $v.content.$error }"
                            :aria-invalid="$v.content.$error"
                            @blur="$v.content.$touch()"
                        />
                        <div v-if="!$v.content.required" class="invalid-tooltip">{{ $t('validation.custom.dearsanta.content.required') }}</div>
                    </div>
                </div>
            </fieldset>
        </ajax-form>
        <table class="table table-hover">
            <thead>
                <tr class="table-active">
                    <th scope="col">
                        {{ $t('dearsanta.list.date') }}
                    </th>
                    <th scope="col">
                        {{ $t('dearsanta.list.body') }}
                    </th>
                    <th scope="col">
                        {{ $t('dearsanta.list.status') }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="email in emails" :key="email.id" class="email">
                    <td>{{ email.created_at }}</td>
                    <td>{{ email.mail_body }}</td>
                    <td>{{ $t(`common.email.status.${email.delivery_status}`) }}</td>
                </tr>
                <tr v-if="emails.length === 0" class="no-email">
                    <td colspan="3">
                        {{ $t('dearsanta.list.empty') }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
