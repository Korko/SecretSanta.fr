<script>
    import Multiselect from '@vueform/multiselect';
    import MyInput from '@/Components/MyInput.vue';

    import { useVuelidate } from '@vuelidate/core';
    import { required, maxLength , email } from '@vuelidate/validators';

    import useErrors from '@/Composables/useErrors.js';

    export default {
        components: {
            Multiselect,
            MyInput
        },
        setup: () => ({ v$: useVuelidate() }),
        validations: () => ({
            name: {
                required,
                maxLength: maxLength(55),
                unique(value) {
                    // standalone validator ideally should not assume a field is required
                    if (value === '') return true;
                    return (this.participants.filter(participant => (participant.name === value)).length === 1);
                }
            },
            email: {
                required,
                maxLength: maxLength(320),
                format: email
            }
        }),
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
            participantOrganizer: {
                type: Boolean,
                default: true
            }
        },
        computed: {
            otherParticipants() {
                var participants = this.all.map((participant, idx) => {participant.idx = idx; return participant;});
                participants.splice(this.idx, 1);
                return participants.filter(participant => !!participant.name);
            }
        },
        methods: {
            fieldError(field) {
                return useErrors(key => this.$t('validation.custom.randomform.participant.'+key), this.v$)(field);
            },
            addExclusion(e, participant) {
                this.$emit('addExclusion', participant);
            },
            removeExclusion(e, participant) {
                this.$emit('removeExclusion', participant);
            },
            removeExclusions() {
                this.$emit('removeExclusions');
            }
        }
    };
</script>

<template>
    <tr class="participant" :dusk="'participant'+idx">
        <td class="align-middle">
            <my-input
                type="text"
                :name="'participants[' + idx + '][name]'"
                :placeholder="$t('form.participant.name.placeholder')"
                :modelValue="name"
                class="participant-name"
                :errors="fieldError('name')"
                @update:modelValue="$emit('update:name', $event)"
                @blur="v$.name.$touch()"
            >
                <span class="input-group-prepend counter">
                    <span class="input-group-text"
                        >{{ idx + 1 }}<template v-if="idx === 0 && participantOrganizer"> - {{ $t('form.participant.organizer') }}</template></span
                    >
                </span>
            </my-input>
        </td>
        <td class="border-left align-middle">
            <my-input
                type="email"
                :name="'participants[' + idx + '][email]'"
                :placeholder="$t('form.participant.email.placeholder')"
                :modelValue="email"
                class="participant-email"
                :errors="fieldError('email')"
                @update:modelValue="$emit('update:email', $event)"
                @blur="v$.email.$touch()"
            />
        </td>
        <td class="border-right text-left participant-exclusions-wrapper align-middle">
            <multiselect
                :options="otherParticipants"
                label="name"
                trackBy="name"
                valueProp="idx"
                :value="exclusions"
                :placeholder="$t('form.participant.exclusions.placeholder')"
                :multiple="true"
                :hideSelected="true"
                :searchable="true"
                :strict="false"
                mode="tags"
                :closeOnSelect="true"
                :noOptionsText="$t('form.participant.exclusions.noOptions')"
                :noResultsText="$t('form.participant.exclusions.noResult')"
                @select="addExclusion"
                @deselect="removeExclusion"
                @clear="removeExclusions"
            />
            <select style="display:none" :name="'participants[' + idx + '][exclusions][]'" multiple>
                <option
                    v-for="exclusion in exclusions"
                    :key="exclusion"
                    selected
                >{{ exclusion }}</option>
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

<!-- Don't scope the style, multiselect does not handle it -->
<style>
    @import '@vueform/multiselect/themes/default.css';
</style>
