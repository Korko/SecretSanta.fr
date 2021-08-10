<script>
    import Vue from 'vue';

    import Vuelidate from 'vuelidate';
    import { required } from 'vuelidate/lib/validators'
    Vue.use(Vuelidate);

    import axios from '../partials/axios.js';
    import Echo from '../partials/echo.js';

    import EmailStatus from './emailStatus.vue';
    import DefaultForm from './form.vue';

    export default {
        components: { EmailStatus },
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
                    }) || [];
            },
            checkUpdates() {
                return !!Object.values(this.data.emails).find(
                    email => email.mail.delivery_status !== 'error'
                );
            }
        },
        created() {
            Echo.channel('draw.'+this.data.draw)
                .listen('.pusher:subscription_succeeded', () => {
                    this.fetchState();
                })
                .listen('.mail.update', data => {
                    var key = Object.keys(this.data.emails).find(key => this.data.emails[key].mail.id === data.id);

                    if(key) {
                        this.$set(this.data.emails[key].mail, 'delivery_status', data.delivery_status);
                        this.$set(this.data.emails[key].mail, 'updated_at', data.updated_at);
                    }
                });
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
            reset() {
                this.content = '';
            },
            resend(k) {
                this.$set(this.data.emails[k], 'delivery_status', 'created');

                return axios
                    .post(this.data.resendEmailUrls[this.data.emails[k].id]);
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
            },
            nl2br(str) {
                return str.replace("\n", '<br />');
            }
        }
    };
</script>

<template>
    <div>
        <ajax-form :action="routes.contactUrl" :$v="$v" @success="success" @reset="reset" :autoReset="true">
            <fieldset>
                <div class="form-group">
                    <label for="mailContent">{{ $t('dearSanta.content.label') }}</label>
                    <div class="input-group">
                        <textarea
                            id="mailContent"
                            v-model="content"
                            name="content"
                            :placeholder="$t('dearSanta.content.placeholder')"
                            :class="{ 'form-control': true, 'is-invalid': $v.content.$error }"
                            :aria-invalid="$v.content.$error"
                            @blur="$v.content.$touch()"
                        />
                        <div v-if="!$v.content.required" class="invalid-tooltip">{{ $t('validation.custom.dearSanta.content.required') }}</div>
                    </div>
                </div>
            </fieldset>
        </ajax-form>
        <table class="table table-hover">
            <caption>{{ $t('dearSanta.list.caption') }}</caption>
            <thead>
                <tr class="table-active">
                    <th scope="col">
                        {{ $t('dearSanta.list.date') }}
                    </th>
                    <th scope="col">
                        {{ $t('dearSanta.list.body') }}
                    </th>
                    <th scope="col">
                        {{ $t('dearSanta.list.status') }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(email, k) in emails" :key="email.id" class="email">
                    <td>{{ email.created_at }}</td>
                    <td><p>{{ nl2br(email.mail_body) }}</p></td>
                    <td><email-status :delivery_status="email.delivery_status" :can_redo="false" @redo="resend(k)"/></td>
                </tr>
                <tr v-if="emails.length === 0" class="no-email">
                    <td colspan="3">
                        {{ $t('dearSanta.list.empty') }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
