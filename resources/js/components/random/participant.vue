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
                <div v-if="!v$.name.required" class="invalid-tooltip">{{ $t('validation.custom.randomform.participant.name.required') }}</div>
                <div v-else-if="!v$.name.unique" class="invalid-tooltip">{{ $t('validation.custom.randomform.participant.name.distinct') }}</div>
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
                <div v-if="!v$.email.required" class="invalid-tooltip">{{ $t('validation.custom.randomform.participant.email.required') }}</div>
                <div v-else-if="!v$.email.format" class="invalid-tooltip">{{ $t('validation.custom.randomform.participant.email.format') }}</div>
                <div v-else-if="fieldError(`participants.${idx}.email`)" class="invalid-tooltip">{{ fieldError(`participants.${idx}.email`) }}</div>
            </div>
        </td>
        <td class="border-right text-left participant-exclusions-wrapper align-middle">
            <multiselect
                :options="otherParticipants"
                label="name"
                trackBy="idx"
                valueProp="idx"
                :value="exclusions"
                :placeholder="$t('form.participant.exclusions.placeholder')"
                :multiple="true"
                :hideSelected="true"
                :searchable="true"
                :strict="false"
                mode="tags"
                :closeOnSelect="false"
                :noOptionsText="$t('form.participant.exclusions.noOptions')"
                :noResultsText="$t('form.participant.exclusions.noResult')"
                @select="addExclusion"
                @deselect="removeExclusion"
            />
            <select style="display:none" :name="'participants[' + idx + '][exclusions][]'" multiple>
                <option
                    v-for="exclusion in exclusions"
                    :key="exclusion.idx"
                    :value="exclusion.idx"
                    selected
                >{{ exclusion.idx }}</option>
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

<script>
    import Multiselect from '@vueform/multiselect'

    import { useVuelidate } from '@vuelidate/core';
    import { required, email, maxLength } from '@vuelidate/validators';

    export default {
        components: {
            Multiselect
        },
        setup: () => ({ v$: useVuelidate() }),
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
            exclusionsTxt: {
                type: String,
                default: null
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
        validations: () => ({
            name: {
                required,
                maxLength: maxLength(55),
                unique(value) {
                    // standalone validator ideally should not assume a field is required
                    if (value === '') return true;

                    return (this.all.filter(participant => (participant.name === value)).length === 1);
                }
            },
            email: {
                required,
                maxLength: maxLength(320),
                format: email
            }
        }),
        computed: {
            otherParticipants() {
                var participants = this.all.map((participant, idx) => {participant.idx = idx; return participant;});
                participants.splice(this.idx, 1);
                return participants.filter(participant => !!participant.name);
            }
        },
        mounted: function() {
            if(this.name) this.v$.name.$touch();
            if(this.email) this.v$.email.$touch();
        },
        methods: {
            changeName(value) {
                this.$emit('input:name', value);
            },
            changeEmail(value) {
                this.$emit('input:email', value);
            },
            changeExclusions(value) {
                this.$emit('input:exclusions', value);
            },
            addExclusion(e, participant) {
                this.$emit('addExclusion', participant);
            },
            removeExclusion(e, participant) {
                this.$emit('removeExclusion', participant);
            }
        }
    };
</script>

<style scoped>
    @import '@vueform/multiselect/themes/default.css';
</style>