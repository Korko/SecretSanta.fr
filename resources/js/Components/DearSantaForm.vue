<script>
    import { useVuelidate } from '@vuelidate/core';
    import { required } from '@vuelidate/validators';

    import fetch from '../Modules/fetch.js';
    import Echo from '../Modules/echo.js';
    import { deepMerge } from '../helpers.js';

    import EmailStatus from './EmailStatus.vue';
    import Tooltip from './Tooltip.vue';
    import AjaxForm from './AjaxForm.vue';

    export default {
        components: { EmailStatus, Tooltip, AjaxForm },
        setup: () => ({ v$: useVuelidate() }),
        validations() {
            return {
                content: {
                    required
                }
            };
        },
        props: {
            draw: {
                type: Object,
                required: true
            },
            participant: {
                type: Object,
                required: true
            },
            emails: {
                type: Object,
                required: true
            },
            routes: {
                type: Object,
                required: true
            },
            targetDearSantaLastUpdate: {
                type: String,
                required: true
            }
        },
        data() {
            return {
                content: ''
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
            finished() {
                return !!this.draw.finished_at;
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
            Echo.channel('draw.'+this.draw.hash)
                .listen('.mail.update', data => {
                    if(this.emails[data.id]) {
                        this.emails[data.id].mail.delivery_status = data.delivery_status;
                        this.emails[data.id].mail.updated_at = data.updated_at;
                    }
                });

            window.localStorage.setItem('secretsanta', JSON.stringify(deepMerge(
                JSON.parse(window.localStorage.getItem('secretsanta')) || {},
                {
                    [this.draw.hash]: {
                        title: this.draw.mail_title,
                        creation: this.draw.created_at,
                        expiration: this.draw.expires_at,
                        organizerName: this.draw.organizer_name,
                        links: {
                            [this.participant.hash]: {name: this.participant.name, link: window.location.href}
                        }
                    }
                }
            )));
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

                return fetch(this.routes.resendEmail[id]);
            },
            resend_target() {
                this.targetDearSantaLastUpdate = new Date().getTime();

                return fetch(this.routes.resendTarget);
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
        <div v-if="finished" class="alert alert-warning" role="alert">
            {{ $t('dearsanta.finished', {finished_at: endDateLong}) }}
        </div>
        <AjaxForm :action="routes.contactUrl" :v$="v$" @success="success" @reset="reset" :autoReset="true">
            <fieldset>
                <div class="form-group">
                    <label for="mailContent">{{ $t('dearsanta.content.label') }}</label>
                    <div class="input-group">
                        <textarea
                            id="mailContent"
                            v-model="content"
                            name="content"
                            :placeholder="$t('dearsanta.content.placeholder')"
                            :class="{ 'form-control': true, 'is-invalid': v$.content.$error }"
                            :aria-invalid="v$.content.$error"
                            @blur="v$.content.$touch()"
                        />
                        <div v-if="!v$.content.required" class="invalid-tooltip">{{ $t('validation.custom.dearSanta.content.required') }}</div>
                    </div>
                </div>
            </fieldset>
        </AjaxForm>
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
                    <td><EmailStatus :delivery_status="email.mail.delivery_status" :last_update="email.mail.updated_at" @redo="resend(email.id)"/></td>
                </tr>
                <tr v-if="emails.length === 0" class="no-email">
                    <td colspan="3">
                        {{ $t('dearsanta.list.empty') }}
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="text-center">
            <Tooltip v-if="recentTargetDearSanta" direction="top">
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
            </Tooltip>
            <button v-else class="btn btn-info btn-lg" @click="resend_target">
                <i class="fas fa-redo" />
                {{ $t('dearsanta.resend.button') }}
            </button>
        </div>
    </div>
</template>

<style scoped>
    #container{
        height:800px;
        background:#eff3f7;
        margin:-76px auto 0 auto;
        font-size:0;
        overflow:hidden;
    }
    aside{
        position: absolute;
        width:260px;
        height:800px;
        background-color:#4D5760;
        display:inline-block;
        font-size:15px;
        vertical-align:top;
    }
    main{
        width: 100%;
        height: 100%;
        padding-left:260px;
        height:800px;
        display:inline-block;
        font-size:15px;
        vertical-align:top;
    }

    aside ul{
        padding-left:0;
        margin:0;
        list-style-type:none;
        overflow-y:scroll;
        height:690px;
    }
    aside li{
        padding:10px 0;
    }
    aside li:hover{
        background-color:#5e616a;
    }
    h2,h3{
        margin:0;
    }
    aside li img{
        border-radius:50%;
        margin-left:20px;
        margin-right:8px;
    }
    aside li div{
        display:inline-block;
        vertical-align:top;
        margin-top:12px;
    }
    aside li h2{
        font-size:14px;
        color:#fff;
        font-weight:normal;
        margin-bottom:5px;
    }
    aside li h3{
        font-size:12px;
        color:#7e818a;
        font-weight:normal;
    }

    .status{
        width:8px;
        height:8px;
        border-radius:50%;
        display:inline-block;
        margin-right:7px;
    }
    .green{
        background-color:#58b666;
    }
    .orange{
        background-color:#ff725d;
    }
    .blue{
        background-color:#6fbced;
        margin-right:0;
        margin-left:7px;
    }

    main header{
        height:110px;
        padding:30px 20px 30px 40px;
    }
    main header > *{
        display:inline-block;
        vertical-align:top;
    }
    main header img:first-child{
        border-radius:50%;
    }
    main header img:last-child{
        width:24px;
        margin-top:8px;
    }
    main header div{
        margin-left:10px;
        margin-right:145px;
    }
    main header h2{
        font-size:16px;
        margin-bottom:5px;
    }
    main header h3{
        font-size:14px;
        font-weight:normal;
        color:#7e818a;
    }

    #chat{
        padding-left:0;
        margin:0;
        list-style-type:none;
        overflow-y:scroll;
        height:535px;
        border-top:2px solid #fff;
        border-bottom:2px solid #fff;
    }
    #chat li{
        padding:10px 30px;
    }
    #chat h2,#chat h3{
        display:inline-block;
        font-size:13px;
        font-weight:normal;
    }
    #chat h3{
        color:#bbb;
    }
    #chat .entete{
        margin-bottom:5px;
    }
    #chat .message{
        padding:20px;
        color:#fff;
        line-height:25px;
        max-width:90%;
        display:inline-block;
        text-align:left;
        border-radius:5px;
    }
    #chat .me{
      text-align:right;
    }
    #chat .you .message{
        background-color:#58b666;
    }
    #chat .me .message{
        background-color:#6fbced;
    }
    #chat .triangle{
        width: 0;
        height: 0;
        border-style: solid;
        border-width: 0 10px 10px 10px;
    }
    #chat .you .triangle{
        border-color: transparent transparent #58b666 transparent;
        margin-left:15px;
    }
    #chat .me .triangle{
        border-color: transparent transparent #6fbced transparent;
        margin-left:375px;
    }

    main footer{
        height:155px;
        padding:20px 30px 10px 20px;
    }
    main footer textarea{
        resize:none;
        border:none;
        display:block;
        width:100%;
        height:80px;
        border-radius:3px;
        padding:20px;
        font-size:13px;
        margin-bottom:13px;
    }
    main footer textarea::placeholder{
        color:#ddd;
    }
    main footer img{
        height:30px;
        cursor:pointer;
    }
    main footer a{
        text-decoration:none;
        text-transform:uppercase;
        font-weight:bold;
        color:#6fbced;
        vertical-align:top;
        margin-left:333px;
        margin-top:5px;
        display:inline-block;
    }

    #form form {
        margin-bottom: 20px;
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

    .email:hover td p {
        background:
            rgba(0, 0, 0, 0.075),
            rgba(0, 0, 0, 0.075),
            radial-gradient(50% 0, farthest-side, rgba(0,0,0,.2), rgba(0,0,0,0)),
            radial-gradient(50% 100%, farthest-side, rgba(0,0,0,.2), rgba(0,0,0,0)) 0 100%;
        background:
            rgba(0, 0, 0, 0.075),
            rgba(0, 0, 0, 0.075),
            radial-gradient(farthest-side at 50% 0, rgba(0,0,0,.2), rgba(0,0,0,0)),
            radial-gradient(farthest-side at 50% 100%, rgba(0,0,0,.2), rgba(0,0,0,0)) 0 100%;
    }

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
</style>