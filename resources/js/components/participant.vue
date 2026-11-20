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
            participantOrganizer: {
                type: Boolean,
                default: true
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
    <tr class="participant" :dusk="'participant'+idx">
        <td class="align-middle">
            <div class="input-group">
                <span class="input-group-prepend counter">
                    <span class="input-group-text"
                        >{{ idx + 1 }}<template v-if="idx === 0 && participantOrganizer"> - {{ $t('form.participant.organizer') }}</template></span
                    >
                </span>
                <input
                    type="text"
                    :name="'participants[' + idx + '][name]'"
                    :placeholder="$t('form.participant.name.placeholder')"
                    :value="name"
                    class="form-control participant-name"
                    :class="{ 'is-invalid': v$.name.$error || fieldError(`participants.${idx}.name`) }"
                    :aria-invalid="v$.name.$error || fieldError(`participants.${idx}.name`)"
                    @input="changeName($event.target.value)"
                    @blur="v$.name.$touch()"
                />
                <div v-if="v$.name.required.$invalid" class="invalid-tooltip">{{ $t('validation.custom.randomform.participant.name.required') }}</div>
                <div v-else-if="v$.name.unique.$invalid" class="invalid-tooltip">{{ $t('validation.custom.randomform.participant.name.distinct') }}</div>
                <div v-else-if="fieldError(`participants.${idx}.name`)" class="invalid-tooltip">{{ fieldError(`participants.${idx}.name`) }}</div>
            </div>
        </td>
        <td class="border-left align-middle">
            <div class="input-group">
                <input
                    type="email"
                    :name="'participants[' + idx + '][email]'"
                    :placeholder="$t('form.participant.email.placeholder')"
                    :value="email"
                    class="form-control participant-email"
                    :class="{ 'is-invalid': v$.email.$error || fieldError(`participants.${idx}.email`)}"
                    :aria-invalid="v$.email.$error || fieldError(`participants.${idx}.email`)"
                    @input="changeEmail($event.target.value)"
                    @blur="v$.email.$touch()"
                />
                <div v-if="v$.email.required.$invalid" class="invalid-tooltip">{{ $t('validation.custom.randomform.participant.email.required') }}</div>
                <div v-else-if="v$.email.format.$invalid" class="invalid-tooltip">{{ $t('validation.custom.randomform.participant.email.format') }}</div>
                <div v-else-if="fieldError(`participants.${idx}.email`)" class="invalid-tooltip">{{ fieldError(`participants.${idx}.email`) }}</div>
            </div>
            <i18n-t v-if="suggestEmail(email)" keypath="form.participant.email.suggest" tag="small" class="form-text text-muted email-suggest">
                <template #email><a href="#" @click.prevent="changeEmail(suggestEmail(email))">{{ suggestEmail(email) }}</a></template>
            </i18n-t>
        </td>
        <td class="border-right text-left participant-exclusions-wrapper align-middle">
            <multiselect
                :options="otherParticipants"
                label="name"
                track-by="idx"
                :model-value="exclusions"
                :placeholder="derived.length ? '' : $t('form.participant.exclusions.placeholder')"
                :multiple="true"
                :hide-selected="true"
                :preserve-search="true"
                :close-on-select="false"
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
                            >&times;</button>
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
            <select style="display:none" :name="'participants[' + idx + '][exclusions][]'" multiple>
                <option
                    v-for="exclusionIdx in sentExclusions"
                    :key="exclusionIdx"
                    :value="exclusionIdx"
                    selected
                >{{ exclusionIdx }}</option>
            </select>
        </td>
        <td class="participant-remove-wrapper align-middle">
            <button
                type="button"
                class="btn btn-outline-danger participant-remove"
                :disabled="required"
                @click="$emit('delete')"
            >
                <i class="fas fa-minus" /><span> {{ $t('form.participant.remove') }}</span>
            </button>
        </td>
    </tr>
</template>

<style lang="scss">
    .exclusion-tag {
        display: inline-flex;
        align-items: center;
        margin: 0 6px 4px 0;
        border-radius: 4px;
        font-size: 13px;
        font-weight: 600;
        line-height: 1.4;
        vertical-align: middle;

        button {
            background: transparent;
            border: 0;
            color: inherit;
            cursor: pointer;
            padding: 3px 6px;
            line-height: 1;
        }

        &.exclusion-tag-both {
            background: #276749;
            color: #fff;
        }

        &.exclusion-tag-oneway {
            background: #fff;
            color: #276749;
            border: 1px solid #276749;
        }

        &.exclusion-tag-derived {
            background: #eef7f1;
            color: #276749;
            border: 1px dashed #4f8a63;
            padding: 2px 8px;
            gap: 4px;
            cursor: default;
        }

        .exclusion-icon {
            width: 14px;
            height: 14px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2.2;
            stroke-linecap: round;
            stroke-linejoin: round;
            vertical-align: -2px;
        }
    }
</style>

