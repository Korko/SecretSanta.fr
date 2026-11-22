<script>
    import useVuelidate from '@vuelidate/core';

    import { required } from '@vuelidate/validators'

    import alertify, { hardConfirm } from '../partials/alertify.js';
    import fetch from '../partials/fetch.js';
    import Echo from '../partials/echo.js';

    import EmailStatus from './emailStatus.vue';
    import Tooltip from './tooltip.vue';
    import DefaultForm from './form.vue';

    export default {
        setup: () => ({ v$: useVuelidate() }),
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
                channel: this.data.channel,
                emails: this.data.emails,
                resendEmailUrls: this.data.resendEmailUrls,
                resendTargetEmailsUrl: this.data.resendTargetEmailsUrl,
                targetDearSantaLastUpdate: this.data.targetDearSantaLastUpdate,
                targetsRevealed: this.data.targetsRevealed,
                endsAt: this.data.ends_at,
                now: Date.now(),
                report: this.data.report,
                email: this.data.email,
                pendingEmail: this.data.pendingEmail,
                showEmailChange: !!this.data.pendingEmail,
                suggestionRecipients: this.data.suggestionRecipients || {},
                suggestions: this.data.suggestions || {},
                receivedSuggestions: this.data.receivedSuggestions || [],
                showSuggestion: false,
                suggestion: { recipient: '', content: '', signed: false },
                newEmail: '',
                emailCode: ''
            };
        },
        computed: {
            emailsByDate() {
                // Copies: mutating reactive state in a computed re-triggers it forever
                return Object.values(this.emails)
                    .map(email => ({ ...email, created_at: new Date(email.mail.created_at) }))
                    .sort((email1, email2) => email2.created_at - email1.created_at)
                    .map(email => ({ ...email, created_at: email.created_at.toLocaleString(this.$i18n.locale) }));
            },
            checkUpdates() {
                return !!Object.values(this.emails).find(
                    email => email.mail.delivery_status !== 'error'
                );
            },
            // Once the event is over the organizer sees every target, revealed or not
            ended() {
                return this.now >= new Date(this.endsAt).getTime();
            },
            targetsStatus() {
                return this.targetsRevealed ? 'revealed' : (this.ended ? 'ended' : 'secret');
            },
            suggestionsList() {
                return Object.values(this.suggestions)
                    .map(suggestion => ({ ...suggestion, created_at: new Date(suggestion.mail.created_at) }))
                    .sort((a, b) => b.created_at - a.created_at)
                    .map(suggestion => ({ ...suggestion, created_at: suggestion.created_at.toLocaleString(this.$i18n.locale) }));
            },
            receivedSuggestionsList() {
                return [...this.receivedSuggestions]
                    .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
                    .map(suggestion => ({ ...suggestion, created_at: new Date(suggestion.created_at).toLocaleString(this.$i18n.locale) }));
            },
            recentTargetDearSanta() {
                return (new Date()).getTime() - (new Date(this.targetDearSantaLastUpdate)).getTime() < 5*60*1000;
            }
        },
        created() {
            setInterval(() => this.now = Date.now(), 60e3);

            Echo.channel(this.channel)
                .listen('.mail.update', data => {
                    const tracked = this.emails[data.id] || this.suggestions[data.id];
                    if(tracked) {
                        tracked.mail.delivery_status = data.delivery_status;
                        tracked.mail.updated_at = data.updated_at;
                    }
                })
                // Something new (sent from another tab, an idea received for the target): the event carries
                // no content, it is fetched through this page's own secret link
                .listen('.participant.update', () => this.refresh());
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
                this.emails[data.email.mail.id] = data.email;
            },
            reset() {
                this.content = '';
            },
            resend(id) {
                this.emails[id].mail.delivery_status = 'created';
                this.emails[id].mail.updated_at = new Date().getTime();

                return fetch(this.resendEmailUrls[id], 'POST');
            },
            sendReport(reason) {
                return fetch(this.routes.reportUrl, 'POST', reason ? { reason } : {})
                    .then(data => this.report = data.report);
            },
            refresh() {
                return fetch(this.routes.fetchStateUrl).then(data => {
                    this.emails = data.emails;
                    this.suggestions = data.suggestions;
                    this.receivedSuggestions = data.receivedSuggestions;
                });
            },
            requestEmailChange() {
                return fetch(this.routes.requestEmailChangeUrl, 'POST', { email: this.newEmail })
                    .then(data => {
                        this.pendingEmail = data.pendingEmail;
                        this.emailCode = '';
                        alertify.confirmAlert(data.message);
                    })
                    .catch(this.alertError);
            },
            changeEmail() {
                return fetch(this.routes.changeEmailUrl, 'POST', { code: this.emailCode })
                    .then(data => {
                        this.email = data.email;
                        this.pendingEmail = null;
                        this.newEmail = '';
                        this.emailCode = '';
                        this.showEmailChange = false;
                        alertify.confirmAlert(data.message);
                    })
                    .catch(this.alertError);
            },
            sendSuggestion() {
                const { recipient, content, signed } = this.suggestion;
                return fetch(this.routes.suggestUrl, 'POST', { recipient, content, signed: signed ? 1 : 0 })
                    .then(data => {
                        this.suggestions = { ...this.suggestions, [data.suggestion.mail.id]: data.suggestion };
                        this.suggestion = { recipient: '', content: '', signed: false };
                        alertify.confirmAlert(data.message);
                    })
                    .catch(this.alertError);
            },
            confirmWithdraw() {
                // Permanent: typed, like the organizer removing someone
                hardConfirm({
                    title: this.$t('dearsanta.withdraw.confirm.title'),
                    body: this.$t('dearsanta.withdraw.confirm.body', { organizer: this.data.organizer }),
                    word: this.$t('dearsanta.withdraw.confirm.value'),
                    help: this.$t('dearsanta.withdraw.confirm.help'),
                    ok: this.$t('dearsanta.withdraw.confirm.ok'),
                    cancel: this.$t('dearsanta.withdraw.confirm.cancel')
                }).then(this.withdraw);
            },
            withdraw() {
                return fetch(this.routes.withdrawUrl, 'POST')
                    .then(data => alertify.confirmAlert(data.message, () => window.location.pathname = '/'))
                    .catch(this.alertError);
            },
            alertError(error) {
                alertify.errorAlert(Object.values(error.errors || {})[0]?.[0] || error.message);
            },
            resend_target() {
                this.targetDearSantaLastUpdate = new Date().getTime();

                return fetch(this.resendTargetEmailsUrl, 'POST');
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
        <ajax-form :action="routes.contactUrl" :validator="v$" @success="success" @reset="reset" :autoReset="true">
            <fieldset>
                <div class="form-group">
                    <div class="alert alert-info" role="alert">
                        {{  $t('dearsanta.reminder') }}
                    </div>
                    <!-- Discreet on purpose: only matters to participants who care about the organizer seeing the pairs.
                         The organizer's panel, where this page sits for them, says it already -->
                    <p v-if="!data.isOrganizer" class="text-muted small text-right targets-revealed" :title="$t('dearsanta.targets.' + targetsStatus)">
                        <i :class="targetsStatus === 'secret' ? 'fas fa-eye-slash' : 'fas fa-eye'" />
                        {{ $t('dearsanta.targets.' + targetsStatus) }}
                    </p>
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
                        <div v-if="v$.content.required.$invalid" class="invalid-tooltip">{{ $t('validation.custom.dearSanta.content.required') }}</div>
                    </div>
                </div>
            </fieldset>
        </ajax-form>
        <table class="table table-hover" style="margin-top: 20px">
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
                <tr v-for="email in emailsByDate" :key="email.mail.id" class="email">
                    <td>{{ email.created_at }}</td>
                    <td><p v-html="nl2br(e(email.mail_body))"></p></td>
                    <td><email-status :delivery_status="email.mail.delivery_status" :last_update="email.mail.updated_at" @redo="resend(email.mail.id)"/></td>
                </tr>
                <tr v-if="Object.keys(emails).length === 0" class="no-email">
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
            <button v-else-if="targetDearSantaLastUpdate !== null" class="btn btn-info btn-lg" @click="resend_target">
                <i class="fas fa-redo" />
                {{ $t('dearsanta.resend.button') }}
            </button>
        </div>
        <!-- Ideas others gave for this participant's target (also emailed) -->
        <table v-if="receivedSuggestionsList.length" class="table table-hover mt-4 suggestions-received">
            <caption>{{ $t('dearsanta.suggestion.received.title') }}</caption>
            <thead>
                <tr class="table-active">
                    <th scope="col" colspan="3">
                        <i class="fas fa-lightbulb" />
                        {{ $t('dearsanta.suggestion.received.title') }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="idea in receivedSuggestionsList" :key="idea.id" class="suggestion-received">
                    <td>{{ idea.created_at }}</td>
                    <td>{{ idea.sender || $t('dearsanta.suggestion.received.anonymous') }}</td>
                    <td><p v-html="nl2br(e(idea.mail_body))"></p></td>
                </tr>
            </tbody>
        </table>
        <!-- Gift ideas for someone else: emailed to their santa, whom the sender never learns -->
        <div v-if="Object.keys(suggestionRecipients).length" class="suggestions mt-4">
            <div class="text-center">
                <button v-if="!showSuggestion" type="button" class="btn btn-outline-success suggestion-open" @click="showSuggestion = true">
                    <i class="fas fa-lightbulb" />
                    {{ $t('dearsanta.suggestion.open') }}
                </button>
            </div>
            <form v-if="showSuggestion" class="suggestion-form" @submit.prevent="sendSuggestion">
                <p class="text-muted small">{{ $t('dearsanta.suggestion.help') }}</p>
                <div class="form-group">
                    <label for="suggestionRecipient">{{ $t('dearsanta.suggestion.recipient') }}</label>
                    <select id="suggestionRecipient" v-model="suggestion.recipient" required class="form-control">
                        <option value="" disabled>{{ $t('dearsanta.suggestion.choose') }}</option>
                        <option v-for="(name, hash) in suggestionRecipients" :key="hash" :value="hash">{{ name }}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="suggestionContent">{{ $t('dearsanta.suggestion.content') }}</label>
                    <textarea id="suggestionContent" v-model="suggestion.content" required class="form-control" :placeholder="$t('dearsanta.suggestion.placeholder')" />
                </div>
                <div class="form-check mb-2">
                    <input id="suggestionSigned" v-model="suggestion.signed" type="checkbox" class="form-check-input" />
                    <label class="form-check-label" for="suggestionSigned">{{ $t('dearsanta.suggestion.signed') }}</label>
                </div>
                <button type="submit" class="btn btn-success suggestion-send">{{ $t('dearsanta.suggestion.send') }}</button>
            </form>
            <table v-if="suggestionsList.length" class="table table-sm mt-3 suggestions-sent">
                <caption>{{ $t('dearsanta.suggestion.list') }}</caption>
                <tbody>
                    <tr v-for="sent in suggestionsList" :key="sent.mail.id" class="suggestion">
                        <td>{{ sent.created_at }}</td>
                        <td>{{ sent.recipient }}<span v-if="sent.signed" class="text-muted small"> ({{ $t('dearsanta.suggestion.signed_short') }})</span></td>
                        <td><p v-html="nl2br(e(sent.mail_body))"></p></td>
                        <td><email-status :delivery_status="sent.mail.delivery_status" :last_update="sent.mail.updated_at" :disabled="true" /></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- Another mailbox: the new address gets a code first, typed back here to prove it is theirs.
             The organizer changes theirs in the participants list of their panel, where this page sits for them -->
        <div v-if="!data.isOrganizer" class="email-change text-center mt-4 small">
            <span class="text-muted">{{ $t('dearsanta.email.current', { email }) }}</span>
            <button v-if="!showEmailChange" type="button" class="btn btn-link btn-sm email-change-open" @click="showEmailChange = true">{{ $t('dearsanta.email.open') }}</button>
            <form v-if="showEmailChange" class="form-inline justify-content-center mt-2" @submit.prevent="requestEmailChange">
                <label class="sr-only" for="newEmail">{{ $t('dearsanta.email.new') }}</label>
                <input id="newEmail" v-model="newEmail" type="email" required class="form-control form-control-sm mr-2" :placeholder="$t('dearsanta.email.new')" />
                <button type="submit" class="btn btn-outline-primary btn-sm email-change-request">{{ $t('dearsanta.email.send_code') }}</button>
            </form>
            <form v-if="showEmailChange && pendingEmail" class="form-inline justify-content-center mt-2" @submit.prevent="changeEmail">
                <label class="mr-2" for="emailCode">{{ $t('dearsanta.email.code', { email: pendingEmail }) }}</label>
                <input id="emailCode" v-model="emailCode" type="text" inputmode="numeric" autocomplete="one-time-code" required maxlength="7" class="form-control form-control-sm mr-2" />
                <button type="submit" class="btn btn-primary btn-sm email-change-confirm">{{ $t('dearsanta.email.confirm') }}</button>
            </form>
        </div>
        <!-- Wrong recipient or not taking part: the organizer sees it on their panel (they can't be emailed) -->
        <div v-if="!data.isOrganizer" class="report text-center mt-4 small">
            <template v-if="report">
                <p class="text-warning report-sent">
                    <i class="fas fa-flag" />
                    {{ $t('dearsanta.report.sent.' + report, { organizer: data.organizer }) }}
                </p>
                <button type="button" class="btn btn-link btn-sm report-cancel" @click="sendReport(null)">{{ $t('dearsanta.report.cancel') }}</button>
            </template>
            <template v-else>
                <span class="text-muted">{{ $t('dearsanta.report.question') }}</span>
                <button type="button" class="btn btn-link btn-sm report-not-me" @click="sendReport('not_me')">{{ $t('dearsanta.report.not_me') }}</button>
                <button type="button" class="btn btn-link btn-sm report-not-participating" @click="sendReport('not_participating')">{{ $t('dearsanta.report.not_participating') }}</button>
            </template>
            <!-- Signed up but changed their mind: they really leave the draw (a report only tells the organizer) -->
            <div>
                <button type="button" class="btn btn-link btn-sm text-danger withdraw" @click="confirmWithdraw">{{ $t('dearsanta.withdraw.button') }}</button>
            </div>
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
        --lines:  5;
        max-height: calc(var(--lines)*1.5em);
        display: -webkit-box;
        -webkit-line-clamp: var(--lines);
        -webkit-box-orient: vertical;

        background-color: rgba(255,255,255,0);
        background:
            /* Shadow covers */
            linear-gradient(white 30%, rgba(255,255,255,0)),
            linear-gradient(rgba(255,255,255,0), white 70%) 0 100%,

            /* Shadows */
            radial-gradient(50% 0, farthest-side, rgba(0,0,0,.2), rgba(0,0,0,0)),
            radial-gradient(50% 100%, farthest-side, rgba(0,0,0,.2), rgba(0,0,0,0)) 0 100%;
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
        background-color: rgba(0, 0, 0, 0.075);
        background:
            /* Shadow covers */
            linear-gradient(white 30%, rgba(0, 0, 0, 0.075)),
            linear-gradient(rgba(0, 0, 0, 0.075), white 70%) 0 100%,

            /* Shadows */
            radial-gradient(50% 0, farthest-side, rgba(0,0,0,.2), rgba(0,0,0,0)),
            radial-gradient(50% 100%, farthest-side, rgba(0,0,0,.2), rgba(0,0,0,0)) 0 100%;
        background:
            linear-gradient(white 30%, rgba(0, 0, 0, 0.075)),
            linear-gradient(rgba(0, 0, 0, 0.075), white 70%) 0 100%,

            /* Shadows */
            radial-gradient(farthest-side at 50% 0, rgba(0,0,0,.2), rgba(0,0,0,0)),
            radial-gradient(farthest-side at 50% 100%, rgba(0,0,0,.2), rgba(0,0,0,0)) 0 100%;
    }
</style>
