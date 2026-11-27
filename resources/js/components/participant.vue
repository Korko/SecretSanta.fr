<script>
    import useVuelidate from '@vuelidate/core';
    import Multiselect from 'vue-multiselect';
    import { required, email } from '@vuelidate/validators';
    import suggestEmail from '../partials/emailSuggest.js';

    export default {
        setup: () => ({ v$: useVuelidate() }),
        components: {
            Multiselect
        },
        props: {
            idx: {
                type: Number,
                required: true
            },
            name: {
                type: String,
                default: ''
            },
            email: {
                type: String,
                default: ''
            },
            exclusions: {
                type: Array,
                default: () => []
            },
            // Ids of the exclusions that only apply in this direction (the others apply both ways)
            oneWay: {
                type: Array,
                default: () => []
            },
            uid: {
                type: String,
                required: true
            },
            all: {
                type: Array,
                required: true
            },
            required: {
                type: Boolean,
                required: true
            },
            fieldError: {
                type: Function,
                required: true
            },
            // The organizer's own row, in the "you" block: no remove button
            orga: {
                type: Boolean,
                default: false
            }
        },
        validations() {
            return {
                name: {
                    required,
                    // standalone validator ideally should not assume a field is required
                    unique: value => !value || this.all.filter(participant => participant.name === value).length === 1
                },
                email: {
                    required,
                    format: email
                }
            };
        },
        computed: {
            otherParticipants() {
                var participants = this.all.map((participant, idx) => {participant.idx = idx; return participant;});
                participants.splice(this.idx, 1);
                return participants.filter(participant => !!participant.name && !this.derived.includes(participant));
            },
            // Excluded both ways from another row: shown here, not editable here
            derived() {
                return this.all.filter(participant => participant.id !== this.uid
                    && (participant.exclusions || []).some(exclusion => exclusion.id === this.uid)
                    && !(participant.oneWay || []).includes(this.uid)
                    && !this.exclusions.some(exclusion => exclusion.id === participant.id));
            },
            // What the server gets: the reverse exclusions are sent as if typed in this row
            sentExclusions() {
                return [...this.exclusions, ...this.derived]
                    .map(participant => this.all.indexOf(participant))
                    .filter(idx => idx !== -1);
            }
        },
        created: function() {
            if(this.name) this.v$.name.$touch();
            if(this.email) this.v$.email.$touch();
        },
        methods: {
            suggestEmail,
            changeName(value) {
                this.$emit('input:name', value);
            },
            changeEmail(value) {
                this.$emit('input:email', value);
            },
            changeExclusions(value) {
                this.$emit('input:exclusions', value);
            },
            isOneWay(option) {
                return this.oneWay.includes(option.id);
            }
        }
    };
</script>

<template>
    <!-- Desktop: one grid row under the column headers; mobile: one card, its labels visible -->
    <div class="participant" :class="{ 'participant--orga': orga, 'participant--vide': !name && !email }" :dusk="'participant'+idx">
        <span class="kt-num" :class="{ 'kt-num--empty': !name }" aria-hidden="true">{{ idx + 1 }}</span>
        <div class="participant__entete">
            <span class="kt-num" :class="{ 'kt-num--empty': !name }" aria-hidden="true">{{ idx + 1 }}</span>
            <span class="participant__titre">{{ orga ? $t('form.k.you') : $t('form.k.person', { n: idx + 1 }) }}</span>
            <button
                v-if="!orga"
                type="button"
                class="participant__retirer participant-remove"
                :disabled="required"
                :aria-label="name ? $t('form.k.remove', { name }) : $t('form.k.removeN', { n: idx + 1 })"
                @click="$emit('delete')"
            ><svg aria-hidden="true" focusable="false" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3" /></svg></button>
        </div>
        <div class="participant__champ">
            <label :for="uid + '-name'">{{ $t('form.participant.name.label') }}<span class="sr-only"> ({{ idx + 1 }})</span></label>
            <input
                :id="uid + '-name'"
                type="text"
                :name="'participants[' + idx + '][name]'"
                :placeholder="$t('form.participant.name.placeholder')"
                :value="name"
                class="field participant-name"
                :class="{ 'is-invalid': v$.name.$error || fieldError(`participants.${idx}.name`) }"
                :aria-invalid="v$.name.$error || !!fieldError(`participants.${idx}.name`)"
                @input="changeName($event.target.value)"
                @blur="v$.name.$touch()"
            />
            <div v-if="v$.name.required.$invalid" class="invalid-tooltip">{{ $t('validation.custom.randomform.participant.name.required') }}</div>
            <div v-else-if="v$.name.unique.$invalid" class="invalid-tooltip">{{ $t('validation.custom.randomform.participant.name.distinct') }}</div>
            <div v-else-if="fieldError(`participants.${idx}.name`)" class="invalid-tooltip">{{ fieldError(`participants.${idx}.name`) }}</div>
        </div>
        <div class="participant__champ">
            <label :for="uid + '-email'">{{ $t('form.participant.email.label') }}<span class="sr-only"> ({{ idx + 1 }})</span></label>
            <input
                :id="uid + '-email'"
                type="email"
                :name="'participants[' + idx + '][email]'"
                :placeholder="$t('form.participant.email.placeholder')"
                :value="email"
                class="field participant-email"
                :class="{ 'is-invalid': v$.email.$error || fieldError(`participants.${idx}.email`)}"
                :aria-invalid="v$.email.$error || !!fieldError(`participants.${idx}.email`)"
                @input="changeEmail($event.target.value)"
                @blur="v$.email.$touch()"
            />
            <div v-if="v$.email.required.$invalid" class="invalid-tooltip">{{ $t('validation.custom.randomform.participant.email.required') }}</div>
            <div v-else-if="v$.email.format.$invalid" class="invalid-tooltip">{{ $t('validation.custom.randomform.participant.email.format') }}</div>
            <div v-else-if="fieldError(`participants.${idx}.email`)" class="invalid-tooltip">{{ fieldError(`participants.${idx}.email`) }}</div>
            <i18n-t v-if="suggestEmail(email)" keypath="form.participant.email.suggest" tag="span" class="email-suggest" role="status">
                <template #email><a href="#" @click.prevent="changeEmail(suggestEmail(email))">{{ suggestEmail(email) }}</a></template>
            </i18n-t>
        </div>
        <div class="participant__champ k-exclusions participant-exclusions-wrapper">
            <label :for="uid + '-exclusions'">{{ $t('form.participant.exclusions.label') }}<span class="sr-only"> ({{ idx + 1 }})</span></label>
            <multiselect
                :id="uid + '-exclusions'"
                :options="otherParticipants"
                label="name"
                track-by="idx"
                :model-value="exclusions"
                :placeholder="derived.length ? '' : $t('form.participant.exclusions.placeholder')"
                :multiple="true"
                :hide-selected="true"
                :preserve-search="true"
                :close-on-select="false"
                :show-labels="false"
                @select="$emit('addExclusion', $event)"
                @remove="$emit('removeExclusion', $event)"
            >
                <!-- The reverse exclusions from other rows show with this row's own ones -->
                <template #selection="{ values, remove }">
                    <div v-if="values.length || derived.length" class="multiselect__tags-wrap">
                        <span
                            v-for="other in derived"
                            :key="'derived' + other.id"
                            class="exclusion-tag exclusion-tag-derived"
                            :title="$t('form.participant.exclusions.derived', { name: other.name })"
                            @mousedown.prevent
                        >
                            <svg class="exclusion-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M7 7h12l-3-3M17 17H5l3 3" /></svg>
                            {{ other.name }}
                        </span>
                        <span
                            v-for="option in values"
                            :key="option.id"
                            class="exclusion-tag"
                            :class="isOneWay(option) ? 'exclusion-tag-oneway' : 'exclusion-tag-both'"
                            @mousedown.prevent
                        >
                            <button
                                type="button"
                                class="exclusion-tag-direction"
                                :title="$t(isOneWay(option) ? 'form.participant.exclusions.oneWay' : 'form.participant.exclusions.both')"
                                :aria-label="$t(isOneWay(option) ? 'form.participant.exclusions.oneWay' : 'form.participant.exclusions.both')"
                                @mousedown.prevent.stop
                                @click.stop="$emit('toggleDirection', option)"
                            >
                                <template v-if="isOneWay(option)"><svg class="exclusion-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14l-4-4M19 12l-4 4" /></svg></template>
                                <template v-else><svg class="exclusion-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M7 7h12l-3-3M17 17H5l3 3" /></svg></template>
                            </button>
                            <span>{{ option.name }}</span>
                            <button
                                type="button"
                                class="exclusion-tag-remove"
                                :aria-label="$t('form.participant.exclusions.remove', { name: option.name })"
                                @mousedown.prevent.stop
                                @click.stop="remove(option)"
                            ><svg aria-hidden="true" focusable="false" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18" /></svg></button>
                        </span>
                    </div>
                </template>
                <template #noOptions>
                    {{ $t('form.participant.exclusions.noOptions') }}
                </template>
                <template #noResult>
                    {{ $t('form.participant.exclusions.noResult') }}
                </template>
            </multiselect>
            <select hidden :name="'participants[' + idx + '][exclusions][]'" multiple>
                <option
                    v-for="exclusionIdx in sentExclusions"
                    :key="exclusionIdx"
                    :value="exclusionIdx"
                    selected
                >{{ exclusionIdx }}</option>
            </select>
        </div>
        <button
            v-if="!orga"
            type="button"
            class="participant__retirer participant-remove"
            :disabled="required"
            :aria-label="name ? $t('form.k.remove', { name }) : $t('form.k.removeN', { n: idx + 1 })"
            @click="$emit('delete')"
        ><svg aria-hidden="true" focusable="false" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3" /></svg></button>
        <span v-else aria-hidden="true" />
    </div>
</template>
