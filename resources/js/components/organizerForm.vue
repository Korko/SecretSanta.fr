<script>


    import { required, email } from '@vuelidate/validators';

    import { download } from '../partials/helpers.js';

    import { dateFromNow, ymd } from '../partials/helpers.js';

    import alertify, { hardConfirm } from '../partials/alertify.js';
    import fetch from '../partials/fetch.js';
    import Echo from '../partials/echo.js';

    import InputEdit from './inputEdit.vue';
    import EmailStatus from './emailStatus.vue';
    import Tooltip from './tooltip.vue';
    import DefaultForm from './form.vue';

    export default {
        components: { InputEdit, EmailStatus, Tooltip },
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
                draw: this.data.draw,
                expires_at: this.data.expires_at,
                deleted_at: this.data.deleted_at,
                drawn_at: this.data.drawn_at,
                finalCsvAvailable: this.data.finalCsvAvailable,
                changeEmailUrls: this.data.changeEmailUrls,
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
                newParticipant: { name: '', email: '' }
            };
        },
        computed: {
            // Adding or removing someone changes a random santa's target: the later, the more likely they already bought a gift
            changeWarning() {
                // Calendar days, so a draw made yesterday evening reads "yesterday" this morning
                const midnight = d => new Date(d).setHours(0, 0, 0, 0);
                const days = Math.max(0, Math.round((midnight(Date.now()) - midnight(this.drawn_at)) / 864e5));
                const when = new Intl.RelativeTimeFormat(document.documentElement.lang, { numeric: 'auto' }).format(-days, 'day');
                return '<div class="alert alert-warning mt-2 mb-0"><i class="fas fa-exclamation-triangle"></i> '
                    + this.$t('organizer.edit.warning', {when}) + '</div>';
            },
            canEdit() {
                return !this.expired;
            },
            receivedCount() {
                return Object.values(this.participants).filter(p => p.mail.delivery_status === 'received').length;
            },
            failedKeys() {
                return Object.keys(this.participants).filter(k => this.participants[k].mail.delivery_status === 'error' && !this.participants[k].report); // a reported address is refused, it needs fixing
            },
            checkUpdates() {
                return !!Object.values(this.participants).find(
                    participant => participant.mail.delivery_status !== 'error'
                );
            },
            expired() {
                return new Date(this.expires_at) < new Date();
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
                return new Date(this.deleted_at).toLocaleString(this.$i18n.locale, {day: 'numeric', month: 'long', year: 'numeric'});
            }
        },
        created() {
            Echo.channel('draw.'+this.draw)
                .listen('.mail.update', data => {
                    var key = Object.keys(this.participants).find(key => this.participants[key].mail.id === data.id);

                    if(key) {
                        this.participants[key].mail.delivery_status = data.delivery_status;
                        this.participants[key].mail.updated_at = data.updated_at;
                    }
                });
        },
        methods: {
            confirmPurge() {
                let body;
                if(this.finalCsvAvailable && !this.expired) {
                    body = this.$t('organizer.purge.confirm.body_final'); // Won't be able to download final recap + dearSanta
                } else if(this.expired) {
                    body = this.$t('organizer.purge.confirm.body_expired'); // Won't be able to download recap anymore
                } else {
                    body = this.$t('organizer.purge.confirm.body_nofinal'); // Won't be able to download recap anymore + DearSanta
                }

                hardConfirm({
                    title: this.$t('organizer.purge.confirm.title', {deletion: this.deletionDateLong}),
                    body,
                    word: this.$t('organizer.purge.confirm.value'),
                    help: this.$t('organizer.purge.confirm.help'),
                    ok: this.$t('organizer.purge.confirm.ok'),
                    cancel: this.$t('organizer.purge.confirm.cancel')
                }).then(this.purge);
            },
            purge() {
                return fetch(this.routes.deleteUrl, 'DELETE')
                    .then(data => alertify.confirmAlert(data.message, () => window.location.pathname = '/'));
            },
            updateEmail(k, email) {
                this.participants[k].email = email;
                this.participants[k].mail.delivery_status = 'created';
                this.participants[k].mail.updated_at = new Date().getTime();

                return fetch(this.changeEmailUrls[this.participants[k].hash], 'POST', {
                    email: email
                });
            },
            confirmRevealTargets() {
                alertify.confirm(
                    this.$t('organizer.reveal.confirm.title'),
                    this.$t('organizer.reveal.confirm.body'),
                    () => this.revealTargets(),
                    () => {}
                ).set('labels', { ok: this.$t('organizer.reveal.confirm.ok'), cancel: this.$t('organizer.reveal.confirm.cancel') });
            },
            changeDate(event) {
                const input = event.target;
                return fetch(this.routes.changeDateUrl, 'POST', { expires_at: input.value })
                    .then(data => {
                        this.expires_at = data.expires_at;
                        this.deleted_at = data.deleted_at;
                    })
                    .catch(error => {
                        input.value = this.expirationDateShort;
                        alertify.alert(error.errors?.expires_at?.[0] || error.message);
                    });
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
                hardConfirm({
                    title: this.$t('organizer.withdraw.confirm.title', {deletion: this.deletionDateLong}),
                    body: this.$t('organizer.withdraw.confirm.body', {name: this.participants[k].name}) + this.changeWarning,
                    word: this.$t('organizer.withdraw.confirm.value'),
                    help: this.$t('organizer.withdraw.confirm.help'),
                    ok: this.$t('organizer.withdraw.confirm.ok'),
                    cancel: this.$t('organizer.withdraw.confirm.cancel')
                }).then(() => this.submitChanges([hash], []));
            },
            addParticipant() {
                const participant = { ...this.newParticipant };
                if (this.multiMode) {
                    this.pendingAdd.push(participant);
                    this.newParticipant = { name: '', email: '' };
                    return;
                }
                alertify.confirm(
                    this.$t('organizer.edit.confirm_add.title', {name: participant.name}),
                    this.$t('organizer.edit.confirm_add.body', {name: participant.name}) + this.changeWarning,
                    () => this.submitChanges([], [participant]).then(() => this.newParticipant = { name: '', email: '' }),
                    () => {}
                );
            },
            toggleMultiMode() {
                this.multiMode = !this.multiMode;
                this.pendingRemove = [];
                this.pendingAdd = [];
            },
            confirmMultiChanges() {
                alertify.confirm(
                    this.$t('organizer.edit.confirm_multi.title'),
                    this.$t('organizer.edit.confirm_multi.body', {add: this.pendingAdd.length, remove: this.pendingRemove.length}) + this.changeWarning,
                    () => this.submitChanges(this.pendingRemove, this.pendingAdd).then(this.toggleMultiMode),
                    () => {}
                );
            },
            submitChanges(remove, add) {
                // The fetch helper sends flat FormData: name the array entries like a form would
                const data = {};
                remove.forEach((hash, i) => data['remove['+i+']'] = hash);
                add.forEach((p, i) => { data['add['+i+'][name]'] = p.name; data['add['+i+'][email]'] = p.email; });

                return fetch(this.routes.changeParticipantsUrl, 'POST', data)
                    .then(data => {
                        this.participants = data.participants;
                        this.changeEmailUrls = data.changeEmailUrls;
                        this.targets = data.targets;
                        alertify.confirmAlert(this.$t('organizer.edit.done'));
                    })
                    .catch(error => {
                        alertify.errorAlert(Object.values(error.errors || {})[0]?.[0] || error.message);
                        return Promise.reject(error);
                    });
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
    <div>
        <div v-if="expired" class="alert alert-warning" role="alert">
            {{ $t('organizer.expired', {expires_at: expirationDateLong}) }}
        </div>
        <div v-else class="form-group">
            <label for="expires_at">{{ $t('organizer.date.label') }}</label>
            <input id="expires_at" type="date" class="form-control w-auto" :value="expirationDateShort" :min="tomorrow" @change="changeDate" />
            <small class="form-text text-muted">{{ $t('organizer.date.help', {deleted_at: deletionDateLong}) }}</small>
        </div>
        <p class="received-count">
            <i class="fas fa-check" />
            {{ $t('organizer.list.received_count', { received: receivedCount, total: Object.keys(participants).length }) }}
        </p>
        <table class="table table-hover">
            <caption>{{ $t('organizer.list.caption') }}</caption>
            <thead>
                <tr class="table-active">
                    <th style="width: 33%" scope="col">
                        {{ $t('organizer.list.name') }}
                    </th>
                    <th style="width: 33%" scope="col">
                        {{ $t('organizer.list.email') }}
                    </th>
                    <th v-if="targets" scope="col">
                        {{ $t('organizer.list.target') }}
                    </th>
                    <th :style="canEdit ? 'width: 30%' : 'width: 33%'" scope="col">
                        {{ $t('organizer.list.status') }}
                    </th>
                    <th v-if="canEdit" style="width: 3%" scope="col">
                        {{ $t('organizer.list.withdraw') }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(participant, k) in participants" :key="participant.hash" :class="{ 'pending-remove': pendingRemove.includes(participant.hash) }">
                    <td>
                        {{ participant.name }}
                        <span v-if="participant.report" class="badge badge-warning participant-report" :title="$t('organizer.report.help.' + participant.report)">
                            <i class="fas fa-flag" /> {{ $t('organizer.report.' + participant.report) }}
                        </span>
                    </td>
                    <td>
                        <input-edit
                            :value="participant.email"
                            :validation="validations.email"
                            :update="(email) => updateEmail(k, email)"
                            :disabled="expired"
                        >
                            <template #errors="{ v }">
                                <div v-if="v.required.$invalid" class="invalid-tooltip">{{ $t('validation.custom.organizer.email.required') }}</div>
                                <div v-else-if="v.format.$invalid" class="invalid-tooltip">{{ $t('validation.custom.organizer.email.format') }}</div>
                            </template>
                        </input-edit>
                    </td>
                    <td v-if="targets" class="target">{{ targets[participant.hash] }}</td>
                    <td><email-status :delivery_status="participant.mail.delivery_status" :last_update="participant.mail.updated_at" :disabled="expired" @redo="updateEmail(k, participant.email)" /></td>
                    <td v-if="canEdit">
                        <button
                            type="button"
                            class="btn btn-outline-danger participant-remove"
                            @click="confirmWithdrawal(k)"
                        >
                            <template v-if="pendingRemove.includes(participant.hash)"><i class="fas fa-undo" /><span> {{ $t('organizer.edit.undo') }}</span></template>
                            <template v-else><i class="fas fa-minus" /><span> {{ $t('organizer.withdraw.button') }}</span></template>
                        </button>
                    </td>
                </tr>
                <tr v-for="(participant, i) in pendingAdd" :key="'new'+i" class="pending-add">
                    <td>{{ participant.name }} <span class="badge badge-success">{{ $t('organizer.edit.new') }}</span></td>
                    <td>{{ participant.email }}</td>
                    <td v-if="targets" />
                    <td />
                    <td>
                        <button type="button" class="btn btn-outline-danger" @click="pendingAdd.splice(i, 1)">
                            <i class="fas fa-undo" /><span> {{ $t('organizer.edit.undo') }}</span>
                        </button>
                    </td>
                </tr>
            </tbody>
            <tfoot v-if="canEdit">
                <tr>
                    <td><input v-model.trim="newParticipant.name" form="addParticipant" class="form-control participant-add-name" required :placeholder="$t('organizer.list.name')" /></td>
                    <td><input v-model.trim="newParticipant.email" form="addParticipant" type="email" class="form-control participant-add-email" required :placeholder="$t('organizer.list.email')" /></td>
                    <td :colspan="targets ? 3 : 2">
                        <form id="addParticipant" @submit.prevent="addParticipant">
                            <button type="submit" class="btn btn-outline-success participant-add">
                                <i class="fas fa-plus" /><span> {{ $t('organizer.edit.add') }}</span>
                            </button>
                        </form>
                    </td>
                </tr>
            </tfoot>
        </table>
        <div v-if="canEdit" class="multi-mode mb-3">
            <template v-if="multiMode">
                <button type="button" class="btn btn-success multi-validate" :disabled="!pendingAdd.length && !pendingRemove.length" @click="confirmMultiChanges">
                    <i class="fas fa-check" />
                    {{ $t('organizer.edit.validate', {add: pendingAdd.length, remove: pendingRemove.length}) }}
                </button>
                <button type="button" class="btn btn-outline-secondary" @click="toggleMultiMode">
                    {{ $t('organizer.edit.cancel') }}
                </button>
            </template>
            <button v-else type="button" class="btn btn-outline-primary multi-toggle" @click="toggleMultiMode">
                <i class="fas fa-tasks" />
                {{ $t('organizer.edit.multi') }}
            </button>
        </div>
        <button type="button" class="btn btn-warning" :disabled="expired || !failedKeys.length" @click="resendFailed">
            <i class="fas fa-redo" />
            {{ $t('organizer.list.resend_failed', { count: failedKeys.length }) }}
        </button>
        <button v-if="!targets" type="button" class="btn btn-outline-secondary" @click="confirmRevealTargets">
            <i class="fas fa-eye" />
            {{ $t('organizer.reveal.button') }}
        </button>
        <p v-else class="text-muted small">
            <i class="fas fa-eye" />
            {{ $t('organizer.reveal.done', { date: new Date(targetsRevealedAt).toLocaleString($i18n.locale) }) }}
        </p>
        <button type="button" class="btn btn-danger" @click="confirmPurge">
            <i class="fas fa-trash" />
            {{ $t('organizer.purge.button') }}
        </button>
        <template v-if="data.finalCsvAvailable">
            <tooltip direction="right">
                <template #tooltip>
                    <img class="media-object" src="../../images/rune-haugseng-UCzjZPCGV1Y-unsplash.webp" />
                    <div class="text-content">
                        <h3>{{ $t('organizer.download.button_initial-tooltip.title') }}</h3>
                        <p>{{ $t('organizer.download.button_initial-tooltip.content') }}</p>
                    </div>
                </template>
                <template #default>
                    <button type="button" class="btn btn-primary" @click="download">
                        <i class="fas fa-download" />
                        {{ $t('organizer.download.button_initial') }}
                    </button>
                </template>
            </tooltip>
            <tooltip direction="right">
                <template #tooltip>
                    <img class="media-object" src="../../images/mike-arney-9r-_2gzP37k-unsplash.webp" />
                    <div class="text-content">
                        <h3>{{ $t('organizer.download.button_final-tooltip.title') }}</h3>
                        <p>{{ $t('organizer.download.button_final-tooltip.explain') }}</p>
                        <p class="border border-white border-1 rounded pl-2 pr-2 font-italic">{{ $t('organizer.download.button_final-tooltip.limit', {expires_at: expirationDateLong, deleted_at: deletionDateLong}) }}</p>
                    </div>
                </template>
                <template #default>
                    <button :disabled="!expired && !targetsRevealedAt" type="button" class="btn btn-primary" @click="downloadPlus">
                        <i class="fas fa-download" />
                        {{ $t('organizer.download.button_final') }}
                    </button>
                </template>
            </tooltip>
        </template>
        <tooltip v-else direction="right">
            <template #tooltip>
                <img class="media-object" src="../../images/rune-haugseng-UCzjZPCGV1Y-unsplash.webp" />
                <div class="text-content">
                    <h3>{{ $t('organizer.download.button-tooltip.title') }}</h3>
                    <p>{{ $t('organizer.download.button-tooltip.content') }}</p>
                </div>
            </template>
            <template #default>
                <button type="button" class="btn btn-primary" @click="download">
                    <i class="fas fa-download" />
                    {{ $t('organizer.download.button') }}
                </button>
            </template>
        </tooltip>
    </div>
</template>

<style scoped>

    .table td {
        vertical-align: middle !important;
    }

    .pending-remove td:not(:last-child) {
        text-decoration: line-through;
        opacity: .5;
    }

    table caption {
        display: none;
    }

    .purge .dg-btn--ok {
      color: #a82824;
      background-color: #fefefe;
      border-color: #a82824;
    }
</style>
