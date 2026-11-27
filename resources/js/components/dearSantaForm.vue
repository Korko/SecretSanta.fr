<script>
    import useVuelidate from '@vuelidate/core';

    import { required } from '@vuelidate/validators'

    import alertify from '../partials/alertify.js';
    import fetch from '../partials/fetch.js';
    import { follow } from '../partials/echo.js';

    import DefaultForm from './form.vue';
    import Icon from './kraft/icon.vue';
    import GiftTag from './kraft/giftTag.vue';
    import Status from './kraft/status.vue';
    import Ribbon from './kraft/ribbon.vue';
    import KDialog from './kraft/dialog.vue';

    export default {
        setup: () => ({ v$: useVuelidate() }),
        components: { Icon, GiftTag, Status, Ribbon, KDialog },
        extends: DefaultForm,
        props: {
            data: {
                type: Object,
                default() { return {}; }
            },
            routes: {
                type: Object,
                required: true
            },
            // Within the organizer's panel ("Ma participation"): their panel has the banner already
            embedded: {
                type: Boolean,
                default: false
            }
        },
        data() {
            return {
                content: '',
                channel: this.data.channel,
                emails: this.data.emails,
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
                targetMessages: this.data.targetMessages || [],
                // Hidden on this device only: someone may read over their shoulder
                targetHidden: this.readTargetHidden(),
                showSuggestion: false,
                suggestion: { recipient: '', content: '' },
                newEmail: '',
                emailCode: ''
            };
        },
        computed: {
            targetName() {
                return this.targetHidden ? '?' : this.data.target;
            },
            // Days left until the event day (its date, whatever the browser timezone)
            daysLeft() {
                const [y, m, d] = this.data.event.expires_at.split('-').map(Number);
                const today = new Date(this.now);
                return Math.round((new Date(y, m - 1, d) - new Date(today.getFullYear(), today.getMonth(), today.getDate())) / 864e5);
            },
            eventDate() {
                return this.longDate(this.data.event.expires_at);
            },
            // Their own messages to their santa, oldest first like a conversation
            santaThread() {
                return Object.values(this.emails)
                    .map(email => ({ ...email, date: email.mail.created_at }))
                    .sort((a, b) => new Date(a.date) - new Date(b.date));
            },
            targetThread() {
                return [...this.targetMessages].sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
            },
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
                    .map(suggestion => ({ ...suggestion, created_at: this.shortDate(suggestion.created_at) }));
            },
            recentTargetDearSanta() {
                return (new Date()).getTime() - (new Date(this.targetDearSantaLastUpdate)).getTime() < 5*60*1000;
            }
        },
        created() {
            setInterval(() => this.now = Date.now(), 60e3);

            follow(this.channel, this.routes.broadcastAuthUrl, {
                '.mail.update': data => {
                    const tracked = this.emails[data.id] || this.suggestions[data.id];
                    if(tracked) {
                        tracked.mail.delivery_status = data.delivery_status;
                        tracked.mail.updated_at = data.updated_at;
                    }
                },
                // Something new (sent from another tab, an idea received for the target): the event carries
                // no content, it is fetched through this page's own secret link
                '.participant.update': () => this.refresh()
            }, () => this.refresh(), () =>
                Object.values(this.emails).some(email => email.mail.delivery_status !== 'received')
                // A gift idea's sender never sees it received: sent is as far as it goes
                || Object.values(this.suggestions).some(sent => !['sent', 'received'].includes(sent.mail.delivery_status))
            );
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
            sendReport(reason) {
                return fetch(this.routes.reportUrl, 'POST', reason ? { reason } : {})
                    .then(data => {
                        this.report = data.report;
                    })
                    .catch(this.alertError);
            },
            refresh() {
                return fetch(this.routes.fetchStateUrl).then(data => {
                    this.emails = data.emails;
                    this.suggestions = data.suggestions;
                    this.receivedSuggestions = data.receivedSuggestions;
                    this.targetMessages = data.targetMessages || [];
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
                const { recipient, content } = this.suggestion;
                return fetch(this.routes.suggestUrl, 'POST', { recipient, content })
                    .then(data => {
                        this.suggestions = { ...this.suggestions, [data.suggestion.mail.id]: data.suggestion };
                        this.suggestion = { recipient: '', content: '' };
                        alertify.confirmAlert(data.message);
                    })
                    .catch(this.alertError);
            },
            confirmWithdraw() {
                // Permanent: their own name typed, which someone who got another person's email won't do without noticing
                this.$refs.dialog.open({
                    title: this.$t('pages.withdraw.title'),
                    body: this.$t('pages.withdraw.body', { organizer: this.data.organizer }),
                    warning: this.$t('pages.withdraw.warning', { organizer: this.data.organizer }),
                    word: this.data.participant.name,
                    ok: this.$t('pages.withdraw.ok'),
                    danger: true
                }).then(this.withdraw, () => {});
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
            readTargetHidden() {
                try {
                    return localStorage.getItem('ss-hide-target-' + this.data.participant?.hash) === '1';
                } catch (e) {
                    return false;
                }
            },
            toggleTarget() {
                this.targetHidden = !this.targetHidden;
                try {
                    localStorage.setItem('ss-hide-target-' + this.data.participant.hash, this.targetHidden ? '1' : '0');
                } catch (e) {
                    // Private browsing: shown again next time
                }
            },
            // "le 2 décembre"
            shortDate(date) {
                return new Date(date).toLocaleDateString(this.$i18n.locale, { day: 'numeric', month: 'long' });
            },
            // A bare day (YYYY-MM-DD, read as UTC midnight): shown in UTC, or it would be the day before west of UTC
            longDate(day) {
                return new Date(day).toLocaleDateString(this.$i18n.locale, { day: 'numeric', month: 'long', year: 'numeric', timeZone: 'UTC' });
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
    <div class="pg">
        <template v-if="!embedded">
            <section class="pg-banner" aria-labelledby="t-event">
                <div class="kt-veil" aria-hidden="true" />
                <div class="pg-banner-in">
                    <div class="pg-banner-text">
                        <p class="pg-eyebrow">{{ $t('pages.participant.eyebrow', { name: data.participant.name }) }}</p>
                        <h1 id="t-event" class="pg-h1">{{ data.title }}</h1>
                        <ul class="pg-meta" :aria-label="$t('pages.event.label')">
                            <li><icon name="calendar" :size="16" :stroke="2.2" />{{ eventDate }}</li>
                            <li v-if="data.event.location"><icon name="pin" :size="16" :stroke="2.2" />{{ data.event.location }}</li>
                            <li v-if="data.event.budget"><icon name="euro" :size="16" :stroke="2.2" />{{ $t('pages.event.budget', { budget: data.event.budget }) }}</li>
                            <li v-if="daysLeft >= 0" class="pg-meta-count"><icon name="clock" :size="16" :stroke="2.2" />{{ $t(daysLeft === 0 ? 'pages.event.today' : 'pages.event.left', { n: daysLeft }, daysLeft) }}</li>
                        </ul>
                    </div>
                    <p v-if="daysLeft > 0" class="pg-count" :aria-label="$t('pages.event.left_long', { n: daysLeft }, daysLeft)">
                        <span>{{ $t('pages.event.count_before') }}</span><b>{{ daysLeft }}</b><span>{{ $t('pages.event.count_after', daysLeft) }}</span>
                    </p>
                </div>
            </section>
            <ribbon />
        </template>

        <div :class="{ 'pg-main': !embedded }">
            <div class="pg-grid">
                <div class="pg-col">
                    <!-- Their target, hidden on demand (on this device only) -->
                    <section class="pg-card" aria-labelledby="t-cible">
                        <h2 id="t-cible" class="pg-h2"><span class="pg-ico pg-ico--red"><icon name="gift" :size="20" /></span>{{ $t('pages.participant.target.title') }}</h2>
                        <div class="pg-tagrow">
                            <gift-tag :label="$t('pages.gift.for')" :value="targetName" :aria-label="targetHidden ? $t('pages.participant.target.hidden_label') : null" />
                            <button type="button" class="pg-link pg-link--ink target-toggle" :aria-pressed="targetHidden ? 'true' : 'false'" @click="toggleTarget">
                                <icon :name="targetHidden ? 'eye' : 'eyeOff'" :size="16" />{{ targetHidden ? $t('pages.participant.target.show') : $t('pages.participant.target.hide') }}
                            </button>
                        </div>
                        <i18n-t v-if="!targetHidden" keypath="pages.participant.target.text" tag="p" class="pg-p">
                            <template #name><strong>{{ data.target }}</strong></template>
                        </i18n-t>
                        <p v-else class="pg-p">{{ $t('pages.participant.target.text_hidden') }}</p>

                        <h3 class="pg-h3">{{ targetHidden ? $t('pages.participant.target.thread_hidden') : $t('pages.participant.target.thread', { name: data.target }) }}</h3>
                        <ol v-if="targetThread.length" class="pg-thread target-messages">
                            <li v-for="message in targetThread" :key="message.id" class="pg-msg pg-msg--in">
                                <p class="pg-msg-h"><strong>{{ targetHidden ? $t('pages.participant.target.someone') : data.target }}</strong><span>{{ $t('pages.on', { date: shortDate(message.created_at) }) }}</span></p>
                                <p v-html="nl2br(e(message.mail_body))" />
                            </li>
                        </ol>
                        <p v-else class="pg-small no-target-message">{{ targetHidden ? $t('pages.participant.target.empty_hidden') : $t('pages.participant.target.empty', { name: data.target }) }}</p>
                        <!-- Their target's messages and the ideas for them, again in one email -->
                        <p v-if="targetDearSantaLastUpdate !== null" class="pg-small pg-resend">
                            <button type="button" class="pg-link pg-link--ink resend-target" :disabled="recentTargetDearSanta" @click="resend_target">{{ $t('pages.participant.target.resend') }}</button>
                            <span v-if="recentTargetDearSanta" role="status"> {{ $t('common.email.recent') }}</span>
                        </p>
                    </section>

                    <section class="pg-card suggestions-received" aria-labelledby="t-idees">
                        <h2 id="t-idees" class="pg-h2"><span class="pg-ico pg-ico--gold"><icon name="gift" :size="20" /></span>{{ $t('pages.participant.ideas.title', { name: targetName }) }}</h2>
                        <p class="pg-p">{{ $t('pages.participant.ideas.text') }}</p>
                        <ul v-if="receivedSuggestionsList.length" class="pg-ideas">
                            <li v-for="idea in receivedSuggestionsList" :key="idea.id" class="pg-idea suggestion-received">
                                <gift-tag small :label="$t('pages.gift.from')" value="?" :aria-label="$t('pages.gift.anonymous')" />
                                <p v-html="nl2br(e(idea.mail_body))" />
                                <p class="pg-small">{{ $t('pages.on', { date: idea.created_at }) }}</p>
                            </li>
                        </ul>
                        <p v-else class="pg-small">{{ $t('pages.participant.ideas.empty') }}</p>
                    </section>

                    <section class="pg-card" aria-labelledby="t-santa">
                        <h2 id="t-santa" class="pg-h2"><span class="pg-ico pg-ico--green"><icon name="mail" :size="20" /></span>{{ $t('pages.participant.santa.title') }}</h2>
                        <div class="pg-tagrow">
                            <gift-tag :label="$t('pages.gift.from')" value="?" :aria-label="$t('pages.participant.santa.tag_label')" />
                            <span class="pg-small">{{ $t('pages.participant.santa.surprise', { date: eventDate }) }}</span>
                        </div>
                        <p class="pg-p">{{ $t('pages.participant.santa.text') }}</p>
                        <ol v-if="santaThread.length" class="pg-thread">
                            <li v-for="email in santaThread" :key="email.mail.id" class="pg-msg pg-msg--out email">
                                <p class="pg-msg-h"><strong>{{ $t('pages.participant.you') }}</strong><span>{{ $t('pages.on', { date: shortDate(email.date) }) }}</span><status :mail="email.mail" /></p>
                                <p v-html="nl2br(e(email.mail_body))" />
                            </li>
                        </ol>
                        <p v-else class="pg-small no-email">{{ $t('pages.participant.santa.empty') }}</p>
                        <ajax-form :action="routes.contactUrl" :validator="v$" class="pg-compose santa-compose" :auto-reset="true" @success="success" @reset="reset">
                            <label for="mailContent">{{ $t('pages.participant.santa.write') }}</label>
                            <textarea
                                id="mailContent"
                                v-model="content"
                                name="content"
                                class="field"
                                :placeholder="$t('pages.participant.santa.placeholder')"
                                :class="{ 'is-invalid': v$.content.$error }"
                                :aria-invalid="v$.content.$error"
                                @blur="v$.content.$touch()"
                            />
                            <div v-if="v$.content.required.$invalid" class="invalid-tooltip">{{ $t('validation.custom.dearSanta.content.required') }}</div>
                            <template #buttons="{ sending }">
                                <div class="pg-compose-row">
                                    <p class="pg-hint"><icon name="lock" :size="14" /><span>{{ $t('pages.participant.santa.hint') }}</span></p>
                                    <span class="tag-wrap" style="--hole: var(--color-papier)"><button type="submit" class="tag-action tag-action--moyen" :disabled="sending"><icon name="send" :size="18" />{{ $t('pages.send') }}</button></span>
                                </div>
                            </template>
                        </ajax-form>
                    </section>
                </div>

                <aside class="pg-col" :aria-label="$t('pages.aside')">
                    <!-- Gift ideas for someone else: emailed to their santa, whom the sender never learns -->
                    <section v-if="Object.keys(suggestionRecipients).length" class="pg-card pg-card--side suggestions" aria-labelledby="t-sug">
                        <h2 id="t-sug" class="pg-h3"><span class="pg-ico pg-ico--kraft"><icon name="gift" :size="18" /></span>{{ $t('pages.participant.suggest.title') }}</h2>
                        <p class="pg-small">{{ $t('pages.participant.suggest.text') }}</p>
                        <form class="pg-compose pg-compose--flat suggestion-form" @submit.prevent="sendSuggestion">
                            <div class="pg-field">
                                <label for="suggestionRecipient">{{ $t('dearsanta.suggestion.recipient') }}</label>
                                <select id="suggestionRecipient" v-model="suggestion.recipient" required class="field">
                                    <option value="" disabled>{{ $t('dearsanta.suggestion.choose') }}</option>
                                    <option v-for="(name, hash) in suggestionRecipients" :key="hash" :value="hash">{{ name }}</option>
                                </select>
                            </div>
                            <div class="pg-field">
                                <label for="suggestionContent">{{ $t('dearsanta.suggestion.content') }}</label>
                                <textarea id="suggestionContent" v-model="suggestion.content" required class="field pg-textarea-sm" :placeholder="$t('dearsanta.suggestion.placeholder')" />
                            </div>
                            <span class="tag-wrap tag-wrap--kraft pg-self-start"><button type="submit" class="tag-kraft suggestion-send">{{ $t('dearsanta.suggestion.send') }}</button></span>
                        </form>
                        <template v-if="suggestionsList.length">
                            <hr class="pg-sep" />
                            <h3 class="pg-h4">{{ $t('dearsanta.suggestion.list') }}</h3>
                            <ul class="pg-sent suggestions-sent">
                                <li v-for="sent in suggestionsList" :key="sent.mail.id" class="suggestion">
                                    <p class="pg-small"><strong>{{ $t('pages.participant.suggest.for', { name: sent.recipient }) }}</strong> · {{ sent.created_at }}</p>
                                    <p class="pg-p" v-html="nl2br(e(sent.mail_body))" />
                                    <status :mail="sent.mail" />
                                </li>
                            </ul>
                        </template>
                    </section>

                    <!-- Discreet on purpose: only matters to participants who care about the organizer seeing the pairs.
                         The organizer's panel, where this page sits for them, says it already -->
                    <section v-if="!data.isOrganizer" class="pg-card pg-card--side pg-card--row targets-revealed">
                        <span class="pg-ico" :class="targetsStatus === 'secret' ? 'pg-ico--green' : 'pg-ico--gold'"><icon :name="targetsStatus === 'secret' ? 'lock' : 'eye'" :size="18" /></span>
                        <div class="pg-stack">
                            <h2 class="pg-h3 pg-h3--sm">{{ $t('pages.participant.secret.' + targetsStatus) }}</h2>
                            <p class="pg-small">{{ $t('pages.participant.secret.' + targetsStatus + '_text', { organizer: data.organizer }) }}</p>
                        </div>
                    </section>

                    <section v-if="!data.isOrganizer" class="pg-card pg-card--side" aria-labelledby="t-set">
                        <h2 id="t-set" class="pg-h3 pg-h3--sm"><span class="pg-ico pg-ico--kraft pg-ico--sm"><icon name="mail" :size="16" /></span>{{ $t('pages.participant.address.title') }}</h2>
                        <!-- Another mailbox: the new address gets a code first, typed back here to prove it is theirs.
                             The organizer changes theirs in the participants list of their panel, where this page sits for them -->
                        <i18n-t keypath="pages.participant.address.current" tag="p" class="pg-small email-change">
                            <template #email><strong class="pg-ink">{{ email }}</strong></template>
                        </i18n-t>
                        <button v-if="!showEmailChange" type="button" class="pg-link pg-self-start email-change-open" @click="showEmailChange = true">{{ $t('dearsanta.email.open') }}</button>
                        <form v-if="showEmailChange" class="pg-compose pg-compose--flat" @submit.prevent="requestEmailChange">
                            <div class="pg-field">
                                <label for="newEmail">{{ $t('dearsanta.email.new') }}</label>
                                <input id="newEmail" v-model="newEmail" type="email" required autocomplete="email" class="field" />
                            </div>
                            <span class="tag-wrap tag-wrap--kraft pg-self-start"><button type="submit" class="tag-kraft email-change-request">{{ $t('dearsanta.email.send_code') }}</button></span>
                        </form>
                        <form v-if="showEmailChange && pendingEmail" class="pg-compose pg-compose--flat" @submit.prevent="changeEmail">
                            <div class="pg-field">
                                <label for="emailCode">{{ $t('dearsanta.email.code', { email: pendingEmail }) }}</label>
                                <input id="emailCode" v-model="emailCode" type="text" inputmode="numeric" autocomplete="one-time-code" required maxlength="7" class="field pg-code" />
                            </div>
                            <span class="tag-wrap tag-wrap--kraft pg-self-start"><button type="submit" class="tag-kraft email-change-confirm">{{ $t('dearsanta.email.confirm') }}</button></span>
                        </form>

                        <hr class="pg-sep" />
                        <!-- Wrong recipient or not taking part: the organizer is told by email and sees it on their panel -->
                        <div class="report">
                            <template v-if="report">
                                <p class="pg-small report-sent"><icon name="flag" :size="14" /> {{ $t('dearsanta.report.sent.' + report, { organizer: data.organizer }) }}</p>
                                <button type="button" class="pg-link pg-link--ink report-cancel" @click="sendReport(null)">{{ $t('dearsanta.report.cancel') }}</button>
                            </template>
                            <details v-else class="pg-details">
                                <summary class="pg-link pg-link--ink report-open"><icon name="flag" :size="16" />{{ $t('dearsanta.report.question') }}</summary>
                                <ul class="pg-problems">
                                    <li><button type="button" class="pg-link pg-link--ink report-not-me" @click="sendReport('not_me')">{{ $t('pages.participant.problem.not_me') }}</button></li>
                                    <li><button type="button" class="pg-link pg-link--ink report-not-participating" @click="sendReport('not_participating')">{{ $t('dearsanta.report.not_participating') }}</button></li>
                                    <!-- Signed up but changed their mind: they really leave the draw (a report only tells the organizer) -->
                                    <li><button type="button" class="pg-link withdraw" @click="confirmWithdraw">{{ $t('dearsanta.withdraw.button') }}</button></li>
                                </ul>
                            </details>
                        </div>
                    </section>
                </aside>
            </div>
        </div>
        <k-dialog ref="dialog" />
    </div>
</template>
