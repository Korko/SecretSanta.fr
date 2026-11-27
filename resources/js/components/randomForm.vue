<script>
    import useVuelidate from '@vuelidate/core';

    import alertify from '../partials/alertify.js';

    import { required, minLength, maxLength, email, requiredIf } from '@vuelidate/validators';

    import Papa from 'papaparse';
    import suggestEmail from '../partials/emailSuggest.js';

    // Global (not scoped): these style elements rendered by child components / outside Vue
    import 'vue-multiselect/dist/vue-multiselect.css';

    import Csv from './csv.vue';
    import AjaxForm from './ajaxForm.vue';
    import Participant from './participant.vue';

    import { dateFromNow } from '../partials/helpers.js';
    import fetch from '../partials/fetch.js';
    import { follow } from '../partials/echo.js';

    export default {
        setup: () => ({ v$: useVuelidate() }),

        directives: {
            // Grow the textarea with its content: CSS field-sizing where supported, JS fallback (Firefox) otherwise.
            // drop this directive once Firefox ships field-sizing
            autosize: {
                mounted(el) {
                    if (CSS.supports('field-sizing', 'content')) {
                        return;
                    }
                    const resize = () => { el.style.height = 'auto'; el.style.height = el.scrollHeight + 'px'; };
                    el.addEventListener('input', resize);
                    resize();
                }
            }
        },

        components: {
            AjaxForm,
            Csv,
            Participant
        },

        data: function() {
            return {
                participantOrganizer: true,
                organizer: {
                    name: '',
                    email: '',
                },
                participants: [],
                title: '',
                content: '',
                expiration: null,
                location: '',
                budget: '',
                // Last value copied from the "event name" field of the top of the page (see mounted)
                heroTitle: '',
                inserted: '',
                logoIcon: '/images/logo-icon.svg',
                now: window.now,
                maxParticipants: window.global.maxParticipants || 50,
                contact: window.global.contact,
                limitCode: window.global.limitCode || null,
                showModal: false,
                importing: false,
                // The draw is solved by the server afterwards (#117): pending, drawn or failed
                drawState: null,
            };
        },

        validations() {
            return {
                organizer: {
                    name: {
                        required: requiredIf(() => !this.participantOrganizer)
                    },
                    email: {
                        required: requiredIf(() => !this.participantOrganizer),
                        format: email
                    }
                },
                // Each participant row validates itself (participant.vue), vuelidate collects child results
                participants: {
                    required,
                    minLength: minLength(3),
                    maxLength: maxLength(this.maxParticipants)
                },
                title: {
                    required
                },
                content: {
                    required,
                    contains: value => value.indexOf('{TARGET}') >= 0
                },
                expiration: {
                    required,
                    format: value => /^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/.test(value),
                    minValue: value => value >= dateFromNow(1, 'day'),
                    maxValue: value => value <= dateFromNow(1, 'year')
                },
                // "Le jour J": both optional
                location: {
                    maxLength: maxLength(255)
                },
                budget: {
                    format: value => value === '' || value === null || (!isNaN(Number(value)) && Number(value) >= 0),
                    max: value => value === '' || value === null || Number(value) <= 100000
                }
            };
        },

        created: function() {
            this.addParticipant();
            this.addParticipant();
            this.addParticipant();
        },

        // The "event name" field of the top of the page (outside Vue) fills the title, as long as it wasn't changed here
        mounted() {
            const hero = document.getElementById('hero-title');
            if (!hero) {
                return;
            }
            const sync = () => {
                if (this.title === '' || this.title === this.heroTitle) {
                    this.title = hero.value;
                }
                this.heroTitle = hero.value;
            };
            hero.addEventListener('input', sync);
            hero.addEventListener('keydown', event => {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    document.querySelector('[data-focus-form]')?.click();
                }
            });
            sync();
        },

        methods: {
            addExclusion(participant, other) {
                participant.exclusions.push(other);
                // The other one already excludes this participant one way: this is the other direction, one way too,
                // so both rows keep showing their own direction (together, both ways)
                if (other.exclusions.includes(participant)) {
                    participant.oneWay = [...participant.oneWay, other.id];
                }
            },

            suggestEmail,
            moment(amount, unit) {
                return dateFromNow(amount, unit);
            },

            toggleDirection(participant, exclusion) {
                participant.oneWay = participant.oneWay.includes(exclusion.id)
                    ? participant.oneWay.filter(id => id !== exclusion.id)
                    : [...participant.oneWay, exclusion.id];
            },
            removeExclusion(participant, exclusion) {
                participant.exclusions = participant.exclusions.filter(other => other !== exclusion);
                participant.oneWay = participant.oneWay.filter(id => id !== exclusion.id);
            },

            resetParticipants() {
                this.participants = [];
            },

            addParticipant(name, email) {
                this.participants.push({
                    name: name ? name.trim() : undefined,
                    email: email ? email.trim() : undefined,
                    id: 'id' + this.participants.length + new Date().getTime(),
                    exclusions: [],
                    oneWay: []
                });
            },

            // Arrow functions: Papa calls these as methods of its config, where "this" isn't the component
            importParticipants(file, encoding) {
                this.importing = true;
                Papa.parse(file, {
                    encoding,
                    error: () => {
                        this.importing = false;
                        alertify.errorAlert(this.$t('form.csv.importError'));
                    },
                    complete: results => {
                        this.importing = false;
                        this.resetParticipants();

                        // Name, email, then the names this participant excludes
                        const rows = results.data.filter(row => row[0] !== '' && row.length >= 2);
                        rows.forEach(row => this.addParticipant(row[0], row[1]));

                        // Once every row is in: an exclusion may name someone further down the file
                        rows.forEach((row, idx) => {
                            this.participants[idx].exclusions = (row[2] || '')
                                .split(',')
                                .map(name => name.trim())
                                .map(name => this.participants.find(participant => participant.name === name))
                                .filter(participant => !!participant);
                            // An imported file states each direction it wants (a final CSV excludes last year's target only)
                            this.participants[idx].oneWay = this.participants[idx].exclusions.map(participant => participant.id);
                        });

                        while (this.participants.length < 3) {
                            this.addParticipant();
                        }

                        alertify.success(this.$t('form.csv.importSuccess'), 3);
                    }
                });
            },

            // The confirmation shows at the top of the form, possibly far above the submit button
            scrollToSuccess() {
                this.$nextTick(() => document.getElementById('success-wrapper').focus());
            },

            // The form is accepted, the draw solved in the background: followed by its private channel,
            // fetched once subscribed (and polled without websockets) not to miss its end
            onSent(response) {
                const tracking = response.tracking;
                this.drawState = tracking.state;
                this.scrollToSuccess();
                if (tracking.state !== 'pending') {
                    return this.onTracking(tracking);
                }

                const headers = { 'X-TRACKING-TOKEN': tracking.token };
                const channel = tracking.channel;
                follow(channel, tracking.authUrl, {
                    '.tracking.update': data => this.onTracking(data),
                }, () => fetch('/tracking/'+tracking.id, 'GET', null, headers).then(data => this.onTracking(data)), () => this.drawState === 'pending', headers);
            },

            onTracking(data) {
                if (this.drawState !== 'pending' && this.drawState !== data.state) {
                    return;
                }
                this.drawState = data.state;

                // Nothing was kept: the form is still filled in, to fix and send again
                if (data.state === 'failed') {
                    this.$refs.form.sent = false;
                    alertify.errorAlert(this.$t('form.k.failed.'+(data.reason || 'error')));
                }
            },

            // {SANTA} / {TARGET} go where the cursor is in the message (at its end otherwise)
            insert(word) {
                const textarea = this.$refs.content;
                const start = textarea && document.activeElement === textarea ? textarea.selectionStart : this.content.length;
                const end = textarea && document.activeElement === textarea ? textarea.selectionEnd : this.content.length;
                this.content = this.content.slice(0, start) + word + this.content.slice(end);
                this.inserted = this.$t('form.k.inserted', { word });
                this.$nextTick(() => {
                    textarea?.focus();
                    textarea?.setSelectionRange(start + word.length, start + word.length);
                });
            },

            // Keeps the caret: a click on a chip would otherwise blur the message first
            keepCaret(event) {
                if (document.activeElement === this.$refs.content) {
                    event.preventDefault();
                }
            },

            // vuelidate 2 doesn't $touch child components: show participant errors on submit
            touchParticipants() {
                [this.$refs.orgaRow, ...(this.$refs.participants || [])].filter(Boolean).forEach(participant => participant.v$.$touch());
            },

            // Props shared by the organizer's row and the others
            rowProps(participant, idx) {
                return {
                    id: participant.id,
                    idx,
                    name: participant.name,
                    email: participant.email,
                    exclusions: participant.exclusions,
                    oneWay: participant.oneWay,
                    uid: participant.id,
                    all: this.participants,
                    required: idx < 3 && this.participants.length <= 3,
                };
            },

            reset() {
                this.drawState = null;
                this.participants = [];
                this.title = '';
                this.content = '';
                this.expiration = null;
                this.location = '';
                this.budget = '';

                this.addParticipant();
                this.addParticipant();
                this.addParticipant();
            }
        }
    };
</script>

<template>
    <div>
        <ajax-form id="randomForm" class="kt-card k-card" action="/" :validator="v$" @reset="reset" @before-submit="touchParticipants" ref="form" @success="onSent">
            <template #default="{ fieldError }">
                <svg class="kt-corner" aria-hidden="true" focusable="false" viewBox="0 0 270 36" preserveAspectRatio="none"><rect width="270" height="36" fill="#C62A2F" /><rect width="270" height="18" fill="#FFFFFF" opacity=".08" /><path d="M0 6H270M0 30H270" stroke="#FFF0DE" stroke-opacity=".6" stroke-width="2" stroke-dasharray="6 5" /></svg>
                <input v-if="limitCode" type="hidden" name="limit" :value="limitCode" />

                <nav class="k-etapes-nav" :aria-label="$t('form.k.stepsNav')">
                    <a href="#k-step-1"><span class="sticker sticker--red sticker--28" aria-hidden="true">1</span>{{ $t('form.k.stepsShort.one') }}</a>
                    <a href="#k-step-2"><span class="sticker sticker--green sticker--28" aria-hidden="true">2</span>{{ $t('form.k.stepsShort.two') }}</a>
                    <a href="#k-step-3"><span class="sticker sticker--gold sticker--28" aria-hidden="true">3</span>{{ $t('form.k.stepsShort.three') }}</a>
                </nav>

                <fieldset id="k-step-1" class="k-fieldset">
                    <legend class="k-legend"><span class="sticker sticker--red sticker--40" aria-hidden="true">1</span>{{ $t('form.k.steps.one') }}</legend>
                    <div class="kt-phead" aria-hidden="true">
                        <span /><span>{{ $t('form.participant.name.label') }}</span><span>{{ $t('form.participant.email.label') }}</span><span>{{ $t('form.participant.exclusions.label') }}</span><span />
                    </div>

                    <div id="organizer" class="orga-bloc">
                        <span class="orga-bloc__titre" :class="{ 'orga-bloc__titre--bureau': participantOrganizer }">{{ $t('form.k.you') }}</span>
                        <participant
                            v-if="participantOrganizer && participants.length"
                            ref="orgaRow"
                            orga
                            v-bind="rowProps(participants[0], 0)"
                            :field-error="fieldError"
                            @input:name="participants[0].name = $event"
                            @input:email="participants[0].email = $event"
                            @removeExclusion="removeExclusion(participants[0], $event)"
                            @addExclusion="addExclusion(participants[0], $event)"
                            @toggleDirection="toggleDirection(participants[0], $event)"
                        />
                        <div v-else class="orga-bloc__champs">
                            <div class="participant__champ">
                                <label class="champ__label" for="organizer-name">{{ $t('form.participant.name.label') }}</label>
                                <input
                                    id="organizer-name"
                                    type="text"
                                    name="organizer[name]"
                                    :placeholder="$t('form.participant.name.placeholder')"
                                    v-model="organizer.name"
                                    class="field participant-name"
                                    :class="{ 'is-invalid': v$.organizer.name.$error || fieldError(`organizer.name`)}"
                                    :aria-invalid="v$.organizer.name.$error || !!fieldError(`organizer.name`)"
                                    @blur="v$.organizer.name.$touch()"
                                />
                                <div v-if="v$.organizer.name.required.$invalid" class="invalid-tooltip">{{ $t('validation.custom.randomform.organizer.name.required') }}</div>
                                <div v-else-if="fieldError(`organizer.name`)" class="invalid-tooltip">{{ fieldError(`organizer.name`) }}</div>
                            </div>
                            <div class="participant__champ">
                                <label class="champ__label" for="organizer-email">{{ $t('form.participant.email.label') }}</label>
                                <input
                                    id="organizer-email"
                                    type="email"
                                    name="organizer[email]"
                                    :placeholder="$t('form.participant.email.placeholder')"
                                    v-model="organizer.email"
                                    class="field participant-email"
                                    :class="{ 'is-invalid': v$.organizer.email.$error || fieldError(`organizer.email`)}"
                                    :aria-invalid="v$.organizer.email.$error || !!fieldError(`organizer.email`)"
                                    @blur="v$.organizer.email.$touch()"
                                />
                                <div v-if="v$.organizer.email.required.$invalid" class="invalid-tooltip">{{ $t('validation.custom.randomform.organizer.email.required') }}</div>
                                <div v-else-if="v$.organizer.email.format.$invalid" class="invalid-tooltip">{{ $t('validation.custom.randomform.organizer.email.format') }}</div>
                                <div v-else-if="fieldError(`organizer.email`)" class="invalid-tooltip">{{ fieldError(`organizer.email`) }}</div>
                                <i18n-t v-if="suggestEmail(organizer.email)" keypath="form.participant.email.suggest" tag="span" class="email-suggest" role="status">
                                    <template #email><a href="#" @click.prevent="organizer.email = suggestEmail(organizer.email)">{{ suggestEmail(organizer.email) }}</a></template>
                                </i18n-t>
                            </div>
                        </div>
                        <label class="org-check">
                            <input v-model="participantOrganizer" type="checkbox" name="participant-organizer" value="1" class="org-box organizer-participates" aria-describedby="org-hint" />
                            <span class="org-check__texte">
                                <span class="org-check__titre">{{ $t('form.k.participate.label') }}</span>
                                <span id="org-hint" class="org-check__aide">{{ $t('form.k.participate.help') }}</span>
                            </span>
                        </label>
                    </div>

                    <transition-group tag="div" name="fade" class="k-lignes" id="participants">
                        <template v-for="(participant, idx) in participants" :key="participant.id">
                            <participant
                                v-if="!(participantOrganizer && idx === 0)"
                                ref="participants"
                                v-bind="rowProps(participant, idx)"
                                :field-error="fieldError"
                                @input:name="participant.name = $event"
                                @input:email="participant.email = $event"
                                @removeExclusion="removeExclusion(participant, $event)"
                                @addExclusion="addExclusion(participant, $event)"
                                @toggleDirection="toggleDirection(participant, $event)"
                                @delete="participants.splice(idx, 1)"
                            />
                        </template>
                    </transition-group>

                    <p class="k-aide exclusions-help">{{ $t('form.participant.exclusions.help') }}</p>
                    <p v-if="participants.length >= maxParticipants" class="k-alerte participants-max" role="status">
                        {{ $t('validation.custom.randomform.participants.max', { max: maxParticipants, email: contact }) }}
                    </p>
                    <div class="k-actions">
                        <span class="tag-wrap tag-wrap--kraft"><button type="button" class="tag-kraft participant-add" :disabled="participants.length >= maxParticipants" @click="addParticipant()"><svg aria-hidden="true" focusable="false" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M5 12h14" /></svg>{{ $t('form.participant.add') }}</button></span>
                        <span class="tag-wrap tag-wrap--kraft"><button type="button" class="tag-kraft participants-import" :disabled="importing" @click="showModal = true"><svg aria-hidden="true" focusable="false" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 15V4M7 9l5-5 5 5M5 15v4h14v-4" /></svg>{{ importing ? $t('form.participants.importing') : $t('form.participants.import') }}</button></span>
                    </div>
                </fieldset>

                <fieldset id="k-step-2" class="k-fieldset">
                    <legend class="k-legend"><span class="sticker sticker--green sticker--40" aria-hidden="true">2</span>{{ $t('form.k.steps.two') }}</legend>
                    <div class="k-bloc-mobile">
                        <div class="champ">
                            <label class="champ__label" for="mailTitle">{{ $t('form.k.eventLabel') }}</label>
                            <input
                                id="mailTitle"
                                type="text"
                                name="title"
                                v-model="title"
                                :placeholder="$t('form.k.eventPlaceholder')"
                                class="field"
                                :class="{ 'is-invalid': v$.title.$error || fieldError('title') }"
                                :aria-invalid="v$.title.$error || !!fieldError('title')"
                                @blur="v$.title.$touch()"
                            />
                            <div class="invalid-tooltip">{{ fieldError('title') || $t('validation.custom.randomform.title.required') }}</div>
                        </div>
                        <div class="champ">
                            <label class="champ__label" for="mailContent">{{ $t('form.k.messageLabel') }}</label>
                            <textarea
                                id="mailContent"
                                ref="content"
                                v-autosize
                                name="content-email"
                                v-model="content"
                                :placeholder="$t('form.k.messagePlaceholder')"
                                rows="4"
                                class="field field--texte"
                                :class="{ 'is-invalid': v$.content.$error || fieldError('content-email') }"
                                :aria-invalid="v$.content.$error || !!fieldError('content-email')"
                                @blur="v$.content.$touch()"
                            />
                            <div v-if="v$.content.required.$invalid" class="invalid-tooltip">{{ $t('validation.custom.randomform.content.required') }}</div>
                            <div v-else-if="v$.content.contains.$invalid" class="invalid-tooltip">{{ $t('validation.custom.randomform.content.contains_text') }}</div>
                            <div v-else-if="fieldError('content-email')" class="invalid-tooltip">{{ fieldError('content-email') }}</div>
                            <div class="k-inserer">
                                <span class="k-inserer__label">{{ $t('form.k.insert') }}</span>
                                <span class="tag-wrap tag-wrap--kraft"><button type="button" class="chip chip--santa" aria-describedby="insert-santa" @mousedown="keepCaret" @click="insert('{SANTA}')">&#123;SANTA&#125;</button></span>
                                <span id="insert-santa">{{ $t('form.k.insertSanta') }}</span>
                                <span class="tag-wrap tag-wrap--kraft"><button type="button" class="chip chip--target" aria-describedby="insert-target" @mousedown="keepCaret" @click="insert('{TARGET}')">&#123;TARGET&#125;</button></span>
                                <span id="insert-target">{{ $t('form.k.insertTarget') }}</span>
                                <span class="sr-only" role="status">{{ inserted }}</span>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <fieldset id="k-step-3" class="k-fieldset">
                    <legend class="k-legend"><span class="sticker sticker--gold sticker--40" aria-hidden="true">3</span>{{ $t('form.k.steps.three') }}</legend>
                    <div class="k-bloc-mobile">
                        <div class="k-jour">
                            <div class="champ">
                                <label class="champ__label" for="expiration">{{ $t('form.k.date') }}</label>
                                <input
                                    id="expiration"
                                    type="date"
                                    name="data-expiration"
                                    v-model="expiration"
                                    class="field"
                                    :class="{ 'is-invalid': v$.expiration.$error || fieldError('data-expiration') }"
                                    :aria-invalid="v$.expiration.$error || !!fieldError('data-expiration')"
                                    @blur="v$.expiration.$touch()"
                                    :min="moment(1, 'day')"
                                    :max="moment(1, 'year')"
                                />
                                <div class="invalid-tooltip" v-if="v$.expiration.required.$invalid">{{ $t('validation.custom.randomform.expiration.required') }}</div>
                                <div class="invalid-tooltip" v-else-if="v$.expiration.format.$invalid">{{ $t('validation.custom.randomform.expiration.format') }}</div>
                                <div class="invalid-tooltip" v-else-if="v$.expiration.minValue.$invalid">{{ $t('validation.custom.randomform.expiration.min') }}</div>
                                <div class="invalid-tooltip" v-else-if="v$.expiration.maxValue.$invalid">{{ $t('validation.custom.randomform.expiration.max') }}</div>
                                <div class="invalid-tooltip" v-else-if="fieldError('data-expiration')">{{ fieldError('data-expiration') }}</div>
                            </div>
                            <div class="champ">
                                <label class="champ__label" for="location">{{ $t('form.k.location') }} <span class="champ__facultatif">{{ $t('form.k.optional') }}</span></label>
                                <input
                                    id="location"
                                    type="text"
                                    name="data-location"
                                    maxlength="255"
                                    v-model="location"
                                    :placeholder="$t('form.k.locationPlaceholder')"
                                    class="field"
                                    :class="{ 'is-invalid': v$.location.$error || fieldError('data-location') }"
                                    :aria-invalid="v$.location.$error || !!fieldError('data-location')"
                                    @blur="v$.location.$touch()"
                                />
                                <div class="invalid-tooltip">{{ fieldError('data-location') || $t('validation.custom.randomform.location.max') }}</div>
                            </div>
                            <div class="champ">
                                <label class="champ__label" for="budget">{{ $t('form.k.budget') }} <span class="champ__facultatif">{{ $t('form.k.optional') }}</span></label>
                                <input
                                    id="budget"
                                    type="number"
                                    name="data-budget"
                                    min="0"
                                    max="100000"
                                    step="any"
                                    inputmode="decimal"
                                    v-model="budget"
                                    class="field"
                                    :class="{ 'is-invalid': v$.budget.$error || fieldError('data-budget') }"
                                    :aria-invalid="v$.budget.$error || !!fieldError('data-budget')"
                                    @blur="v$.budget.$touch()"
                                />
                                <div v-if="v$.budget.max.$invalid" class="invalid-tooltip">{{ $t('validation.custom.randomform.budget.max') }}</div>
                                <div v-else class="invalid-tooltip">{{ fieldError('data-budget') || $t('validation.custom.randomform.budget.format') }}</div>
                            </div>
                        </div>
                        <p class="champ__aide">{{ $t('form.k.dayHelp') }}</p>
                    </div>
                </fieldset>
            </template>

            <template #buttons="{ sending, sent, onReset }">
                <div class="k-envoi">
                    <p v-show="sent" id="success-wrapper" class="k-succes" role="status" tabindex="-1">{{ drawState === 'pending' ? $t('form.k.solving') : $t('form.k.success') }}</p>
                    <span v-if="sent && drawState !== 'pending'" class="tag-wrap tag-wrap--kraft"><button type="button" class="tag-kraft" @click="onReset">{{ $t('form.k.newDraw') }}</button></span>
                    <span v-else-if="!sent" class="tag-wrap" style="--hole: var(--color-papier)"><button type="submit" class="tag-action tag-action--grand" :disabled="sending || sent" :aria-busy="sending || sent">{{ sending ? $t('form.k.sending') : $t('form.k.submit') }}</button></span>
                    <p class="k-sceau"><img :src="logoIcon" alt="" width="28" height="30" />{{ $t('form.k.seal') }}</p>
                </div>
            </template>
        </ajax-form>
        <csv v-if="showModal" @import="importParticipants" @close="showModal = false" />
    </div>
</template>

<style scoped>
    .fade-enter-active, .fade-leave-active, .fade-move {
        transition: all .4s;
    }

    .fade-enter-from, .fade-leave-to {
        opacity: 0;
    }

    @media (prefers-reduced-motion: reduce) {
        .fade-enter-active, .fade-leave-active, .fade-move {
            transition: none;
        }
    }
</style>
