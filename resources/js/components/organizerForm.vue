<script>


    import { required, email } from '@vuelidate/validators';

    import { download } from '../partials/helpers.js';

    import { dateFromNow, ymd, escapeHtml } from '../partials/helpers.js';

    import alertify from '../partials/alertify.js';
    import fetch from '../partials/fetch.js';
    import { follow } from '../partials/echo.js';

    import { markRaw } from 'vue';

    import Icon from './kraft/icon.vue';
    import Status from './kraft/status.vue';
    import Ribbon from './kraft/ribbon.vue';
    import KDialog from './kraft/dialog.vue';
    import ActionsMenu from './kraft/actionsMenu.vue';
    import DefaultForm from './form.vue';
    import Fetched from './fetched.vue';
    import DearSantaForm from './dearSantaForm.vue';

    export default {
        components: { Icon, Status, Ribbon, KDialog, ActionsMenu, Fetched },
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
                participants: this.data.participants,
                // Withdrew from their page: can be brought back
                withdrawn: this.data.withdrawn || [],
                draw: this.data.draw,
                channel: this.data.channel,
                expires_at: this.data.expires_at,
                ends_at: this.data.ends_at,
                now: Date.now(),
                endedByServer: false,
                deleted_at: this.data.deleted_at,
                location: this.data.location || '',
                budget: this.data.budget || '',
                drawn_at: this.data.drawn_at,
                finalCsvAvailable: this.data.finalCsvAvailable,
                changeEmailUrls: this.data.changeEmailUrls,
                changeNameUrls: this.data.changeNameUrls,
                targetsRevealedAt: this.data.targetsRevealedAt,
                targets: this.data.targets,
                validations: {
                    email: {
                        required,
                        format: email
                    }
                },
                // "Several at once" mode: changes wait here until validated, to email each santa only once
                multiMode: false,
                pendingRemove: [],
                pendingAdd: [],
                newParticipant: { name: '', email: '' },
                maxParticipants: this.data.maxParticipants,
                contact: this.data.contact,
                participantForm: markRaw(DearSantaForm),
                // "Organisation" or "Ma participation", when the organizer takes part
                tab: 'organization',
                // "Le jour J": edited here, saved with "Enregistrer"
                day: { date: ymd(this.data.expires_at), location: this.data.location || '', budget: this.data.budget || '' }
            };
        },
        computed: {
            // Adding or removing someone changes a random santa's target: the later, the more likely they already bought a gift
            changeWarning() {
                // Calendar days, so a draw made yesterday evening reads "yesterday" this morning
                const midnight = d => new Date(d).setHours(0, 0, 0, 0);
                const days = Math.max(0, Math.round((midnight(Date.now()) - midnight(this.drawn_at)) / 864e5));
                const when = new Intl.RelativeTimeFormat(document.documentElement.lang, { numeric: 'auto' }).format(-days, 'day');
                return this.$t('organizer.edit.warning', {when});
            },
            // Counting the changes waiting in the "several at once" mode
            atLimit() {
                return Object.keys(this.participants).length + this.pendingAdd.length - this.pendingRemove.length >= this.maxParticipants;
            },
            canEdit() {
                return !this.expired;
            },
            // Taking part, the organizer's participant page is on their panel: gone with the event, or if they removed themselves
            participation() {
                return this.routes.participant && !this.expired && this.participants[this.routes.participant.hash] ? this.routes.participant : null;
            },
            participantsList() {
                return Object.entries(this.participants).map(([k, participant], i) => ({ k, i, ...participant }));
            },
            total() {
                return Object.keys(this.participants).length;
            },
            // "À traiter": failed emails, reports, withdrawals
            todo() {
                const items = [];
                this.participantsList.forEach(p => {
                    if (p.report) {
                        items.push({ kind: p.report, participant: p });
                    } else if (p.mail.delivery_status === 'error') {
                        items.push({ kind: 'error', participant: p });
                    }
                });
                this.withdrawn.forEach(p => items.push({ kind: 'withdrawn', participant: p }));
                return items;
            },
            drawnDate() {
                return new Date(this.drawn_at).toLocaleDateString(this.$i18n.locale, { day: 'numeric', month: 'long' });
            },
            finalAvailable() {
                return this.finalCsvAvailable && (this.expired || !!this.targetsRevealedAt);
            },
            receivedCount() {
                return Object.values(this.participants).filter(p => p.mail.delivery_status === 'received').length;
            },
            wroteCount() {
                return Object.values(this.participants).filter(p => p.wrote_to_santa).length;
            },
            failedKeys() {
                return Object.keys(this.participants).filter(k => this.participants[k].mail.delivery_status === 'error' && !this.participants[k].report); // a reported address is refused, it needs fixing
            },
            checkUpdates() {
                return !!Object.values(this.participants).find(
                    participant => participant.mail.delivery_status !== 'error'
                );
            },
            // The server's instant (end of the event day, its timezone), not the browser's idea of the date,
            // or the server saying so (a browser clock running late)
            expired() {
                return this.endedByServer || this.now >= new Date(this.ends_at).getTime();
            },
            tomorrow() {
                return dateFromNow(1, 'day');
            },
            expirationDateShort() {
                return ymd(this.expires_at);
            },
            expirationDateLong() {
                // A bare day (YYYY-MM-DD, read as UTC midnight): shown in UTC, or it would be the day before west of UTC
                return new Date(this.expires_at).toLocaleString(this.$i18n.locale, {day: 'numeric', month: 'long', year: 'numeric', timeZone: 'UTC'});
            },
            deletionDateLong() {
                return new Date(this.deleted_at).toLocaleString(this.$i18n.locale, {day: 'numeric', month: 'long', year: 'numeric', timeZone: 'UTC'});
            }
        },
        watch: {
            finalCsvAvailable() {
                this.pollFinalCsvAvailable();
            },
            // A tab left open past the event: no more edits, and every target shows (nothing else on the page is lost)
            expired(now, before) {
                if (now && !before && !this.targets) {
                    fetch(this.routes.fetchStateUrl).then(data => this.targets = data.targets);
                }
            }
        },
        created() {
            this.pollFinalCsvAvailable();
            setInterval(() => this.now = Date.now(), 60e3);

            follow(this.channel, {
                '.mail.update': data => {
                    var key = Object.keys(this.participants).find(key => this.participants[key].mail.id === data.id);

                    if(key) {
                        this.participants[key].mail.delivery_status = data.delivery_status;
                        this.participants[key].mail.bounce_reason = data.bounce_reason;
                        this.participants[key].mail.updated_at = data.updated_at;
                    }
                }
            }, () => this.refreshMails(), () => Object.values(this.participants).some(p => p.mail.delivery_status !== 'received'));
        },
        methods: {
            // Missed or unpushed status changes (see follow()): only the mails, the rest has its own updates
            refreshMails() {
                return fetch(this.routes.fetchStateUrl).then(data => {
                    Object.entries(data.participants).forEach(([key, { mail }]) => {
                        if(this.participants[key]) {
                            this.participants[key].mail = mail;
                        }
                    });
                });
            },
            confirmPurge() {
                this.$refs.dialog.open({
                    title: this.$t('pages.purge.title'),
                    body: this.$t('pages.purge.body', { deletion: this.deletionDateLong }),
                    // After the event, the participants can no longer write anyway
                    warning: this.$t(this.expired ? 'pages.purge.warning_expired' : 'pages.purge.warning'),
                    word: this.$t('pages.purge.word'),
                    ok: this.$t('pages.purge.ok'),
                    danger: true
                }).then(this.purge, () => {});
            },
            purge() {
                return fetch(this.routes.deleteUrl, 'DELETE')
                    .then(data => alertify.confirmAlert(data.message, () => window.location.pathname = '/'));
            },
            renameParticipant(k) {
                const oldName = this.participants[k].name;
                this.$refs.dialog.open({
                    title: this.$t('organizer.rename.title', { name: oldName }),
                    body: this.$t('organizer.rename.body'),
                    fields: [{ name: 'name', label: this.$t('organizer.list.name'), value: oldName, required: true }],
                    ok: this.$t('organizer.rename.ok')
                }).then(({ name }) => {
                    name = name.trim();
                    if (name && name !== oldName) {
                        this.updateName(k, name)
                            .then(() => alertify.success(this.$t('organizer.rename.done', { name: escapeHtml(name) })))
                            .catch(error => alertify.errorAlert(escapeHtml(error.errors?.name?.[0] || error.message)));
                    }
                }, () => {});
            },
            // The address fixed, the email goes again (it says nothing to the others)
            fixEmail(k) {
                this.$refs.dialog.open({
                    title: this.$t('pages.fix.title', { name: this.participants[k].name }),
                    body: this.$t('pages.fix.body'),
                    fields: [{ name: 'email', label: this.$t('organizer.list.email'), type: 'email', value: this.participants[k].email, required: true, autocomplete: 'email' }],
                    ok: this.$t('pages.fix.ok')
                }).then(({ email }) => this.updateEmail(k, email.trim()).catch(error => {
                    alertify.errorAlert(escapeHtml(error.errors?.email?.[0] || error.message));
                }), () => {});
            },
            updateName(k, name) {
                const oldName = this.participants[k].name;

                return fetch(this.changeNameUrls[this.participants[k].hash], 'POST', { name })
                    .then(data => {
                        this.participants[k].name = data.participant.name;
                        // Revealed targets show names too
                        if (this.targets) {
                            Object.keys(this.targets).forEach(hash => this.targets[hash] === oldName && (this.targets[hash] = data.participant.name));
                        }
                    });
            },
            updateEmail(k, email) {
                this.participants[k].email = email;
                this.participants[k].mail.delivery_status = 'created';
                this.participants[k].mail.bounce_reason = null;
                this.participants[k].mail.updated_at = new Date().getTime();

                return fetch(this.changeEmailUrls[this.participants[k].hash], 'POST', {
                    email: email
                }).catch(this.checkEnded);
            },
            confirmRevealTargets() {
                // Permanent and visible to every participant: typed, like a withdrawal
                this.$refs.dialog.open({
                    title: this.$t('pages.reveal.title'),
                    body: this.$t('pages.reveal.body'),
                    warning: this.$t('pages.reveal.warning'),
                    word: this.$t('pages.reveal.word'),
                    ok: this.$t('pages.reveal.ok'),
                    danger: true
                }).then(() => this.revealTargets(), () => {});
            },
            // "Enregistrer": the date and the details that changed, each by its existing route
            saveDay() {
                const requests = [];
                if (this.day.date !== this.expirationDateShort) {
                    requests.push(fetch(this.routes.changeDateUrl, 'POST', { expires_at: this.day.date }).then(data => {
                        this.expires_at = data.expires_at;
                        this.ends_at = data.ends_at;
                        this.deleted_at = data.deleted_at;
                    }));
                }
                if (this.day.location !== this.location || String(this.day.budget) !== String(this.budget)) {
                    requests.push(fetch(this.routes.changeEventDetailsUrl, 'POST', { location: this.day.location, budget: this.day.budget }).then(data => {
                        this.location = data.location || '';
                        this.budget = data.budget || '';
                    }));
                }
                return Promise.all(requests)
                    .then(() => alertify.success(this.$t('organizer.eventDetails.saved'), 3))
                    .catch(error => {
                        this.day = { date: this.expirationDateShort, location: this.location, budget: this.budget };
                        alertify.errorAlert(escapeHtml(Object.values(error.errors || {})[0]?.[0] || error.message));
                        this.checkEnded(error).catch(() => {});
                    });
            },
            // Any refused action may be the first sign the event is over
            checkEnded(error) {
                if (error?.expired) {
                    this.endedByServer = true;
                }
                return Promise.reject(error);
            },
            revealTargets() {
                return fetch(this.routes.revealTargetsUrl, 'POST')
                    .then(data => {
                        this.targetsRevealedAt = data.targetsRevealedAt;
                        this.targets = data.targets;
                    });
            },
            // Same as each row's "redo" button, for every failed email at once
            resendFailed() {
                this.failedKeys.forEach(k => this.updateEmail(k, this.participants[k].email));
            },
            confirmWithdrawal(k) {
                const hash = this.participants[k].hash;
                if (this.multiMode) {
                    this.pendingRemove = this.pendingRemove.includes(hash)
                        ? this.pendingRemove.filter(h => h !== hash)
                        : [...this.pendingRemove, hash];
                    return;
                }
                // No word to type any more: the consequences are said, with the date of the draw
                const name = this.participants[k].name;
                this.$refs.dialog.open({
                    title: this.$t('pages.remove.title', { name }),
                    body: this.$t('pages.remove.body', { name }),
                    warning: this.$t('pages.remove.warning', { date: this.drawnDate }),
                    ok: this.$t('pages.remove.ok', { name }),
                    danger: true
                }).then(() => this.submitChanges([hash], []).catch(() => {}), () => {});
            },
            addParticipant() {
                this.$refs.dialog.open({
                    title: this.$t('pages.add.title'),
                    body: this.$t(this.multiMode ? 'pages.add.body_multi' : 'pages.add.body'),
                    warning: this.multiMode ? null : this.changeWarning,
                    fields: [
                        { name: 'name', label: this.$t('organizer.list.name'), required: true },
                        { name: 'email', label: this.$t('organizer.list.email'), type: 'email', required: true, autocomplete: 'email' }
                    ],
                    ok: this.$t('organizer.edit.add')
                }).then(({ name, email }) => {
                    const participant = { name: name.trim(), email: email.trim() };
                    if (this.multiMode) {
                        this.pendingAdd.push(participant);
                        return;
                    }
                    this.submitChanges([], [participant]).catch(() => {});
                }, () => {});
            },
            toggleMultiMode() {
                this.multiMode = !this.multiMode;
                this.pendingRemove = [];
                this.pendingAdd = [];
            },
            confirmMultiChanges() {
                this.$refs.dialog.open({
                    title: this.$t('organizer.edit.confirm_multi.title'),
                    body: this.$t('organizer.edit.confirm_multi.body', {add: this.pendingAdd.length, remove: this.pendingRemove.length}),
                    warning: this.changeWarning,
                    ok: this.$t('organizer.edit.validate', {add: this.pendingAdd.length, remove: this.pendingRemove.length}),
                    danger: true
                }).then(() => this.submitChanges(this.pendingRemove, this.pendingAdd).then(this.toggleMultiMode).catch(() => {}), () => {});
            },
            submitChanges(remove, add, force = false) {
                // The fetch helper sends flat FormData: name the array entries like a form would
                const data = {};
                remove.forEach((hash, i) => data['remove['+i+']'] = hash);
                add.forEach((p, i) => { data['add['+i+'][name]'] = p.name; data['add['+i+'][email]'] = p.email; });
                if (force) {
                    data.force = 1;
                }

                return fetch(this.routes.changeParticipantsUrl, 'POST', data)
                    .then(data => {
                        this.refreshParticipants(data);
                        alertify.confirmAlert(this.$t('organizer.edit.done'));
                    })
                    .catch(error => {
                        // No santa can take the removed person's place without breaking an exclusion: the organizer may force it
                        if (error.errors?.exclusions && !force) {
                            return this.confirmForce(error).then(() => this.submitChanges(remove, add, true), () => Promise.reject(error));
                        }
                        alertify.errorAlert(escapeHtml(Object.values(error.errors || {})[0]?.[0] || error.message));
                        return this.checkEnded(error);
                    });
            },
            // No santa can take someone's place without breaking an exclusion: the organizer may force it
            confirmForce(error) {
                return this.$refs.dialog.open({
                    title: this.$t('organizer.force.title'),
                    body: error.errors.exclusions[0],
                    warning: this.$t('organizer.force.body'),
                    ok: this.$t('pages.force.ok'),
                    danger: true
                });
            },
            // What the server answers after a change of participants: the whole panel data
            refreshParticipants(data) {
                this.participants = data.participants;
                this.withdrawn = data.withdrawn;
                this.changeEmailUrls = data.changeEmailUrls;
                this.changeNameUrls = data.changeNameUrls;
                this.targets = data.targets;
                this.finalCsvAvailable = data.finalCsvAvailable;
            },
            // The final CSV availability is computed out of the request (null until then): asked again until known
            pollFinalCsvAvailable() {
                clearTimeout(this.finalCsvTimer);
                if (this.finalCsvAvailable === null) {
                    this.finalCsvTimer = setTimeout(() => fetch(this.routes.fetchStateUrl)
                        .then(data => this.finalCsvAvailable = data.finalCsvAvailable)
                        .finally(() => this.pollFinalCsvAvailable()), 20e3);
                }
            },
            // Someone else may have got their email and withdrawn in their place: back, at an address to check
            confirmRestore(participant) {
                const name = participant.name;
                this.$refs.dialog.open({
                    title: this.$t('organizer.withdrawn.prompt_title', { name }),
                    body: this.$t('organizer.withdrawn.prompt_body', { name }),
                    warning: this.changeWarning,
                    fields: [{ name: 'email', label: this.$t('organizer.list.email'), type: 'email', value: participant.email, required: true, autocomplete: 'email' }],
                    ok: this.$t('organizer.withdrawn.ok')
                }).then(({ email }) => this.restore(participant, email.trim()), () => {});
            },
            restore(participant, email, force = false) {
                return fetch(this.routes.restoreUrl, 'POST', { participant: participant.hash, email, ...(force ? { force: 1 } : {}) })
                    .then(data => {
                        this.refreshParticipants(data);
                        alertify.success(this.$t('organizer.withdrawn.done', { name: escapeHtml(participant.name) }));
                    })
                    .catch(error => {
                        // Their exclusions leave no place for them: the organizer may force it, as for a removal
                        if (error.errors?.exclusions && !force) {
                            return this.confirmForce(error).then(() => this.restore(participant, email, true), () => {});
                        }
                        alertify.errorAlert(escapeHtml(Object.values(error.errors || {})[0]?.[0] || error.message));
                        return this.checkEnded(error).catch(() => {});
                    });
            },
            // The "⋯" menu of a row
            rowActions(p) {
                const pending = this.pendingRemove.includes(p.hash);
                return [
                    { label: this.$t('pages.menu.rename'), icon: 'pen', class: 'participant-rename', disabled: this.expired, action: () => this.renameParticipant(p.k) },
                    { label: this.$t('pages.fix.button'), icon: 'mail', class: 'participant-fix', disabled: this.expired, action: () => this.fixEmail(p.k) },
                    { label: this.$t('pages.menu.resend'), icon: 'send', class: 'participant-resend', disabled: this.expired, action: () => this.updateEmail(p.k, p.email).then(() => alertify.success(this.$t('pages.menu.resent'), 3), () => {}) },
                    { label: this.$t(pending ? 'organizer.edit.undo' : 'pages.menu.remove'), icon: pending ? 'undo' : 'close', danger: !pending, class: 'participant-remove', disabled: !this.canEdit, action: () => this.confirmWithdrawal(p.k) },
                ];
            },
            // Arrows move between the two tabs
            switchTab() {
                this.tab = this.tab === 'organization' ? 'participation' : 'organization';
                this.$nextTick(() => document.getElementById('tab-' + this.tab)?.focus());
            },
            download() {
                fetch(this.routes.csvInitUrl, 'GET', '', {responseType: 'blob'})
                    .then(response => {
                        download(response, 'secretsanta_'+this.expirationDateShort+'_init.csv', 'text/csv');
                    });
            },
            downloadPlus() {
                fetch(this.routes.csvFinalUrl, 'GET', '', {responseType: 'blob'})
                    .then(response => {
                        download(response, 'secretsanta_'+this.expirationDateShort+'_full.csv', 'text/csv');
                    });
            }
        }
    };
</script>

<template>
    <div class="pg">
        <section class="pg-banner" aria-labelledby="t-org-event">
            <div class="kt-veil" aria-hidden="true" />
            <div class="pg-banner-in">
                <div class="pg-banner-text">
                    <p class="pg-eyebrow">{{ $t('pages.organizer.eyebrow') }}</p>
                    <h1 id="t-org-event" class="pg-h1">{{ data.title }}</h1>
                    <ul class="pg-meta" :aria-label="$t('pages.event.label')">
                        <li><icon name="calendar" :size="16" :stroke="2.2" />{{ expirationDateLong }}</li>
                        <li v-if="location"><icon name="pin" :size="16" :stroke="2.2" />{{ location }}</li>
                        <li v-if="budget"><icon name="euro" :size="16" :stroke="2.2" />{{ $t('pages.event.budget', { budget: budget + ' €' }) }}</li>
                    </ul>
                </div>
                <div class="pg-tracker received-count">
                    <p class="pg-tracker-n"><b>{{ receivedCount }} / {{ total }}</b><span>{{ $t('pages.organizer.received', receivedCount) }}</span></p>
                    <div class="pg-progress" role="progressbar" aria-valuemin="0" :aria-valuemax="total" :aria-valuenow="receivedCount" :aria-label="$t('pages.organizer.received_label')">
                        <span :style="{ width: (total ? Math.round(receivedCount / total * 100) : 0) + '%' }" />
                    </div>
                    <p class="pg-tracker-help">{{ $t('pages.organizer.received_help') }}</p>
                </div>
            </div>
        </section>
        <ribbon />

        <div class="pg-main">
            <!-- Taking part, the organizer's participant page is in their panel: two roles, two tabs -->
            <div v-if="participation" class="pg-tabs" role="tablist" :aria-label="$t('pages.organizer.tabs')" @keydown.right.prevent="switchTab" @keydown.left.prevent="switchTab">
                <button id="tab-organization" type="button" role="tab" class="pg-tab" :aria-selected="tab === 'organization' ? 'true' : 'false'" :tabindex="tab === 'organization' ? 0 : -1" aria-controls="panel-organization" @click="tab = 'organization'">
                    <icon name="user" :size="16" />{{ $t('pages.organizer.tab_organization') }}
                </button>
                <button id="tab-participation" type="button" role="tab" class="pg-tab organizer-participation-tab" :aria-selected="tab === 'participation' ? 'true' : 'false'" :tabindex="tab === 'participation' ? 0 : -1" aria-controls="panel-participation" @click="tab = 'participation'">
                    <icon name="gift" :size="16" />{{ $t('pages.organizer.tab_participation') }}
                </button>
            </div>

            <div v-if="participation" v-show="tab === 'participation'" id="panel-participation" role="tabpanel" aria-labelledby="tab-participation" class="organizer-participation">
                <fetched :form="participantForm" :url="participation.fetchUrl" :routes="participation" :embedded="true" />
            </div>

            <div v-show="tab === 'organization'" id="panel-organization" class="pg-grid" :role="participation ? 'tabpanel' : null" :aria-labelledby="participation ? 'tab-organization' : null">
                <div class="pg-col">
                    <section v-if="expired" class="pg-card pg-card--alert" role="status">
                        <p class="pg-p"><strong>{{ $t('organizer.expired', { expires_at: expirationDateLong }) }}</strong></p>
                    </section>

                    <!-- What needs the organizer, most urgent first -->
                    <section v-if="todo.length" class="pg-card pg-card--alert todo" aria-labelledby="t-todo">
                        <h2 id="t-todo" class="pg-h2"><span class="pg-ico pg-ico--red"><icon name="warning" :size="20" /></span>{{ $t('pages.organizer.todo') }} <span class="pg-status pg-status--err pg-counter">{{ todo.length }}</span></h2>
                        <ul class="pg-todo">
                            <li v-for="item in todo" :key="item.kind + item.participant.hash" :class="'todo-' + item.kind">
                                <span class="pg-todo-t">
                                    <span class="pg-ico pg-ico--sm" :class="{ error: 'pg-ico--red', withdrawn: 'pg-ico--kraft' }[item.kind] || 'pg-ico--gold'"><icon :name="{ error: 'warning', withdrawn: 'undo' }[item.kind] || 'flag'" :size="16" /></span>
                                    <i18n-t :keypath="'pages.organizer.todo_' + item.kind" tag="span">
                                        <template #name><strong>{{ item.participant.name }}</strong></template>
                                        <template #email><strong>{{ item.participant.email }}</strong></template>
                                        <template #reason>{{ item.participant.mail?.bounce_reason ? $t('common.email.bounce.' + item.participant.mail.bounce_reason) : $t('pages.status.error_unknown') }}</template>
                                        <template #date>{{ item.participant.withdrawn_at ? new Date(item.participant.withdrawn_at).toLocaleDateString($i18n.locale, { day: 'numeric', month: 'long' }) : '' }}</template>
                                    </i18n-t>
                                </span>
                                <span v-if="canEdit" class="pg-todo-a">
                                    <button v-if="item.kind === 'withdrawn'" type="button" class="pg-btn participant-restore" @click="confirmRestore(item.participant)"><icon name="undo" :size="16" />{{ $t('organizer.withdrawn.button') }}</button>
                                    <button v-else-if="item.kind === 'not_participating'" type="button" class="pg-btn pg-btn--danger" @click="confirmWithdrawal(item.participant.k)">{{ $t('organizer.withdraw.button') }}</button>
                                    <button v-else type="button" class="pg-btn" @click="fixEmail(item.participant.k)">{{ $t('pages.fix.button') }}</button>
                                </span>
                            </li>
                        </ul>
                        <button v-if="failedKeys.length > 1 && canEdit" type="button" class="pg-btn pg-self-start resend-failed" @click="resendFailed"><icon name="send" :size="16" />{{ $t('organizer.list.resend_failed', { count: failedKeys.length }) }}</button>
                    </section>

                    <section class="pg-card participants" aria-labelledby="t-people">
                        <div class="pg-tagrow">
                            <h2 id="t-people" class="pg-h2"><span class="pg-ico pg-ico--green"><icon name="user" :size="20" /></span>{{ $t('pages.organizer.participants') }} <span class="pg-small pg-counter">{{ total }}</span></h2>
                            <button v-if="canEdit" type="button" class="pg-btn participant-add" :disabled="atLimit" @click="addParticipant"><icon name="plus" :size="16" />{{ $t('organizer.edit.add') }}</button>
                        </div>
                        <p v-if="atLimit && canEdit" class="k-alerte participants-max" role="status">{{ $t('validation.custom.randomform.participants.max', { max: maxParticipants, email: contact }) }}</p>
                        <p class="pg-small">{{ targets ? $t('pages.organizer.targets_shown') : $t('pages.organizer.targets_secret') }}</p>

                        <!-- From 900 px: a table; below, the same rows as cards -->
                        <table class="pg-table">
                            <caption class="sr-only">{{ $t('organizer.list.caption') }}</caption>
                            <thead>
                                <tr>
                                    <th><span class="sr-only">{{ $t('pages.organizer.number') }}</span></th>
                                    <th>{{ $t('organizer.list.name') }}</th>
                                    <th>{{ $t('pages.organizer.email') }}</th>
                                    <th>{{ $t('pages.organizer.reception') }}</th>
                                    <th>{{ $t('organizer.list.target') }}</th>
                                    <th><span class="sr-only">{{ $t('pages.organizer.actions') }}</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="p in participantsList" :key="p.hash" class="participant-row" :class="{ 'pending-remove': pendingRemove.includes(p.hash) }">
                                    <td class="pg-td-num"><span class="kt-num" aria-hidden="true">{{ p.i + 1 }}</span></td>
                                    <td><span class="pg-name participant-name">{{ p.name }}<span v-if="routes.participant && p.hash === routes.participant.hash" class="pg-small">{{ $t('pages.organizer.you') }}</span></span></td>
                                    <td class="pg-td-email">{{ p.email }}</td>
                                    <td><status :mail="p.mail" :report="p.report" detailed /></td>
                                    <td class="target">
                                        <template v-if="targets">{{ targets[p.hash] }}</template>
                                        <span v-else class="pg-secret"><icon name="lock" :size="15" />{{ $t('pages.organizer.secret') }}</span>
                                    </td>
                                    <td class="pg-td-menu"><actions-menu :label="$t('pages.organizer.actions_for', { name: p.name })" :items="rowActions(p)" /></td>
                                </tr>
                                <tr v-for="(p, i) in pendingAdd" :key="'new' + i" class="pending-add">
                                    <td class="pg-td-num"><span class="kt-num kt-num--empty" aria-hidden="true">+</span></td>
                                    <td><span class="pg-name">{{ p.name }}<span class="pg-small">{{ $t('organizer.edit.new') }}</span></span></td>
                                    <td class="pg-td-email">{{ p.email }}</td>
                                    <td colspan="2" />
                                    <td class="pg-td-menu"><button type="button" class="pg-iconbtn" :aria-label="$t('organizer.edit.undo')" @click="pendingAdd.splice(i, 1)"><icon name="undo" :size="16" /></button></td>
                                </tr>
                            </tbody>
                        </table>
                        <ul class="pg-plist" :aria-label="$t('organizer.list.caption')">
                            <li v-for="p in participantsList" :key="p.hash" :class="{ 'pending-remove': pendingRemove.includes(p.hash) }">
                                <span class="kt-num" aria-hidden="true">{{ p.i + 1 }}</span>
                                <span class="pg-strong">{{ p.name }}<span v-if="routes.participant && p.hash === routes.participant.hash" class="pg-small">{{ $t('pages.organizer.you') }}</span></span>
                                <actions-menu :label="$t('pages.organizer.actions_for', { name: p.name })" :items="rowActions(p)" />
                                <span class="pg-sub">
                                    <status :mail="p.mail" :report="p.report" />
                                    <span>{{ p.email }}</span>
                                    <span v-if="targets">→ {{ targets[p.hash] }}</span>
                                </span>
                            </li>
                            <li v-for="(p, i) in pendingAdd" :key="'new' + i" class="pending-add">
                                <span class="kt-num kt-num--empty" aria-hidden="true">+</span>
                                <span class="pg-strong">{{ p.name }} <span class="pg-small">{{ $t('organizer.edit.new') }}</span></span>
                                <button type="button" class="pg-iconbtn" :aria-label="$t('organizer.edit.undo')" @click="pendingAdd.splice(i, 1)"><icon name="undo" :size="16" /></button>
                                <span class="pg-sub">{{ p.email }}</span>
                            </li>
                        </ul>

                        <template v-if="canEdit || withdrawn.length">
                            <hr class="pg-sep" />
                            <div class="pg-stack withdrawn">
                                <template v-if="withdrawn.length">
                                    <h3 class="pg-h3 pg-h3--sm">{{ $t('organizer.withdrawn.title') }}</h3>
                                    <div v-for="p in withdrawn" :key="p.hash" class="pg-withdrawn withdrawn-participant">
                                        <span><strong>{{ p.name }}</strong> <span class="pg-small pg-inline">· {{ p.email }} · {{ $t('pages.on', { date: new Date(p.withdrawn_at).toLocaleDateString($i18n.locale, { day: 'numeric', month: 'long' }) }) }}</span></span>
                                        <button v-if="canEdit" type="button" class="pg-btn participant-restore" @click="confirmRestore(p)"><icon name="undo" :size="16" />{{ $t('organizer.withdrawn.button') }}</button>
                                    </div>
                                </template>
                                <template v-if="canEdit">
                                    <p class="pg-small">{{ $t('pages.organizer.change_help') }}</p>
                                    <div v-if="multiMode" class="pg-actions multi-mode">
                                        <span class="tag-wrap" style="--hole: var(--color-papier)"><button type="button" class="tag-action tag-action--petit multi-validate" :disabled="!pendingAdd.length && !pendingRemove.length" @click="confirmMultiChanges">{{ $t('organizer.edit.validate', { add: pendingAdd.length, remove: pendingRemove.length }) }}</button></span>
                                        <button type="button" class="pg-link pg-link--ink" @click="toggleMultiMode">{{ $t('organizer.edit.cancel') }}</button>
                                    </div>
                                    <button v-else type="button" class="pg-link pg-link--ink pg-self-start multi-toggle" @click="toggleMultiMode">{{ $t('organizer.edit.multi') }}</button>
                                    <p v-if="multiMode" class="pg-small" role="status">{{ $t('pages.organizer.multi_help') }}</p>
                                </template>
                            </div>
                        </template>
                    </section>
                    <!-- "Écrire aux participant(e)s" goes here, with the organizer's messages (to come) -->
                </div>

                <aside class="pg-col" :aria-label="$t('pages.organizer.aside')">
                    <section class="pg-card pg-card--side" aria-labelledby="t-day">
                        <h2 id="t-day" class="pg-h3"><span class="pg-ico pg-ico--red"><icon name="calendar" :size="18" /></span>{{ $t('pages.day.title') }}</h2>
                        <form v-if="!expired" class="pg-stack" @submit.prevent="saveDay">
                            <div class="pg-field"><label for="expires_at">{{ $t('organizer.date.label') }}</label><input id="expires_at" v-model="day.date" class="field" type="date" :min="tomorrow" required /></div>
                            <div class="pg-field"><label for="event_location">{{ $t('pages.day.location') }} <span class="pg-small pg-inline">{{ $t('form.k.optional') }}</span></label><input id="event_location" v-model="day.location" class="field" type="text" maxlength="255" :placeholder="$t('organizer.eventDetails.locationPlaceholder')" /></div>
                            <div class="pg-field"><label for="event_budget">{{ $t('pages.day.budget') }} <span class="pg-small pg-inline">{{ $t('form.k.optional') }}</span></label><input id="event_budget" v-model="day.budget" class="field" type="number" min="0" step="any" inputmode="decimal" /></div>
                            <span class="tag-wrap tag-wrap--kraft pg-self-start"><button type="submit" class="tag-kraft day-save">{{ $t('pages.day.save') }}</button></span>
                        </form>
                        <p class="pg-small">{{ expired ? $t('pages.day.deletion', { date: deletionDateLong }) : $t('pages.day.help', { date: deletionDateLong }) }}</p>
                    </section>

                    <section class="pg-card pg-card--side recaps">
                        <h2 class="pg-h3"><span class="pg-ico pg-ico--kraft"><icon name="download" :size="18" /></span>{{ $t('pages.recaps.title') }}</h2>
                        <button type="button" class="pg-btn pg-self-start csv-init" @click="download"><icon name="download" :size="16" />{{ $t('pages.recaps.init') }}</button>
                        <p class="pg-small">{{ $t('pages.recaps.init_help') }}</p>
                        <template v-if="finalCsvAvailable !== false">
                            <button type="button" class="pg-btn pg-self-start csv-final" :disabled="!finalAvailable" @click="downloadPlus"><icon name="download" :size="16" />{{ $t('pages.recaps.final') }}</button>
                            <p class="pg-small">{{ $t('pages.recaps.final_help', { date: expirationDateLong }) }}</p>
                            <p v-if="finalCsvAvailable === null" class="pg-small" role="status">{{ $t('organizer.download.computing') }}</p>
                        </template>
                    </section>

                    <section class="pg-card pg-card--side reveal">
                        <h2 class="pg-h3"><span class="pg-ico pg-ico--green"><icon :name="targets ? 'eye' : 'lock'" :size="18" /></span>{{ $t('pages.secret.title') }}</h2>
                        <template v-if="!targets">
                            <p class="pg-small">{{ $t('pages.secret.text') }}</p>
                            <button type="button" class="pg-btn pg-self-start reveal-open" @click="confirmRevealTargets"><icon name="eye" :size="16" />{{ $t('pages.secret.button') }}</button>
                        </template>
                        <p v-else-if="targetsRevealedAt" class="pg-small">{{ $t('organizer.reveal.done', { date: new Date(targetsRevealedAt).toLocaleString($i18n.locale) }) }}</p>
                        <p v-else class="pg-small">{{ $t('organizer.reveal.expired') }}</p>
                    </section>

                    <section class="pg-card pg-card--side pg-card--danger purge">
                        <h2 class="pg-h3 pg-h3--sm pg-h3--danger"><span class="pg-ico pg-ico--red pg-ico--sm"><icon name="trash" :size="16" /></span>{{ $t('pages.purge.card') }}</h2>
                        <p class="pg-small">{{ $t(expired ? 'pages.purge.card_text_expired' : 'pages.purge.card_text', { date: deletionDateLong }) }}</p>
                        <button type="button" class="pg-btn pg-btn--danger pg-self-start purge-open" @click="confirmPurge"><icon name="trash" :size="16" />{{ $t('pages.purge.button') }}</button>
                    </section>
                </aside>
            </div>
        </div>
        <k-dialog ref="dialog" />
    </div>
</template>
