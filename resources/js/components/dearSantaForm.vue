<script>
    import Vue from 'vue';

    import Vuelidate from 'vuelidate';
    import { required } from 'vuelidate/lib/validators'
    Vue.use(Vuelidate);

    import fetch from '../partials/fetch.js';
    import Echo from '../partials/echo.js';

    import EmailStatus from './emailStatus.vue';
    import Tooltip from './tooltip.vue';
    import DefaultForm from './form.vue';

    export default {
        components: { EmailStatus, Tooltip },
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
                content: '',
                draw: this.data.draw,
                emails: this.data.emails,
                resendEmailUrls: this.data.resendEmailUrls,
                resendTargetEmailsUrl: this.data.resendTargetEmailsUrl,
                targetDearSantaLastUpdate: this.data.targetDearSantaLastUpdate
            };
        },
        computed: {
            emailsByDate() {
                return Object.values(this.emails)
                    .map(email => Object.assign(email, email.mail))
                    .sort((email1, email2) => (new Date(email1.created_at) > new Date(email2.created_at) ? -1 : 1))
                    .map(email => {
                        email.created_at = new Date(email.created_at).toLocaleString('fr-FR');
                        return email;
                    }) || [];
            },
            checkUpdates() {
                return !!Object.values(this.emails).find(
                    email => email.mail.delivery_status !== 'error'
                );
            },
            recentTargetDearSanta() {
                return (new Date()).getTime() - (new Date(this.targetDearSantaLastUpdate)).getTime() < 5*60*1000;
            }
        },
        created() {
            Echo.channel('draw.'+this.draw)
                .listen('.mail.update', data => {
                    if(this.emails[data.id]) {
                        this.emails[data.id].mail.delivery_status = data.delivery_status;
                        this.emails[data.id].mail.updated_at = data.updated_at;
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
                if(!data.email.updated_at) {
                    data.email.updated_at = new Date();
                }
                this.$set(this.emails, data.email.id, data.email);
            },
            reset() {
                this.content = '';
            },
            resend(id) {
                this.emails[id].mail.delivery_status = 'created';
                this.emails[id].mail.updated_at = new Date().getTime();

                return fetch(this.resendEmailUrls[id]);
            },
            resend_target() {
                this.targetDearSantaLastUpdate = new Date().getTime();

                return fetch(this.resendTargetEmailsUrl);
            },
            nl2br(str) {
                return str.replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1<br />$2');
            },
            e(str) {
                var p = document.createElement("p");
                p.appendChild(document.createTextNode(str));
                return p.innerHTML;
            }
        }
    };
</script>

<template>
    <div>
        <ajax-form :action="routes.contactUrl" :$v="$v" @success="success" @reset="reset" :autoReset="true">
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
                        <div v-if="!$v.content.required" class="invalid-tooltip">{{ $t('validation.custom.dearSanta.content.required') }}</div>
                    </div>
                </div>
            </fieldset>
        </ajax-form>
        <table class="table table-hover">
            <caption>{{ $t('dearsanta.list.caption') }}</caption>
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
                <tr v-for="email in emailsByDate" :key="email.id" class="email">
                    <td>{{ email.created_at }}</td>
                    <td><p v-html="nl2br(e(email.mail_body))"></p></td>
                    <td><email-status :delivery_status="email.mail.delivery_status" :last_update="email.mail.updated_at" @redo="resend(email.id)"/></td>
                </tr>
                <tr v-if="emails.length === 0" class="no-email">
                    <td colspan="3">
                        {{ $t('dearsanta.list.empty') }}
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="text-center">
            <tooltip v-if="recentTargetDearSanta" direction="top">
                <template #tooltip>
                    <div class="text-content">
                        {{ $t(`common.email.recent`) }}
                    </div>
                </template>
                <template #default>
                    <button :disabled="true" type="button" class="btn btn-outline-secondary">
                        <i class="fas fa-redo" />
                        {{ $t('dearsanta.resend.button') }}
                    </button>
                </template>
            </tooltip>
            <button v-else class="btn btn-info btn-lg" @click="resend_target">
                <i class="fas fa-redo" />
                {{ $t('dearsanta.resend.button') }}
            </button>
        </div>
    </div>
</template>

<style scoped>
    .no-email {
        text-align: center;
    }

    table {
        table-layout: fixed;
    }

    table th:first-child, table th:last-child {
        width: 12em;
    }

    table caption {
        display: none;
    }

    .email td p {
        overflow: auto;
        --lines:  15;
        max-height: calc(var(--lines)*1.5em);
        display: -webkit-box;
        -webkit-line-clamp: var(--lines);
        -webkit-box-orient: vertical;

        background:
            /* Shadow covers */
            linear-gradient(white 30%, rgba(255,255,255,0)),
            linear-gradient(rgba(255,255,255,0), white 70%) 0 100%,

            /* Shadows */
            radial-gradient(50% 0, farthest-side, rgba(0,0,0,.2), rgba(0,0,0,0)),
            radial-gradient(50% 100%,farthest-side, rgba(0,0,0,.2), rgba(0,0,0,0)) 0 100%;
        background:
            /* Shadow covers */
            linear-gradient(white 30%, rgba(255,255,255,0)),
            linear-gradient(rgba(255,255,255,0), white 70%) 0 100%,

            /* Shadows */
            radial-gradient(farthest-side at 50% 0, rgba(0,0,0,.2), rgba(0,0,0,0)),
            radial-gradient(farthest-side at 50% 100%, rgba(0,0,0,.2), rgba(0,0,0,0)) 0 100%;
        background-repeat: no-repeat;
        background-size: 100% 40px, 100% 40px, 100% 14px, 100% 14px;

        /* Opera doesn't support this in the shorthand */
        background-attachment: local, local, scroll, scroll;
    }
</style>