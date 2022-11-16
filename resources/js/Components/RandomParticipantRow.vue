<script>
    import Multiselect from '@vueform/multiselect'

    export default {
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
        computed: {
            otherParticipants() {
                var participants = this.all.map((participant, idx) => {participant.idx = idx; return participant;});
                participants.splice(this.idx, 1);
                return participants.filter(participant => !!participant.name);
            }
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
                    :class="{ 'is-invalid': fieldError(`participants.${idx}.name`) }"
                    :aria-invalid="fieldError(`participants.${idx}.name`)"
                    @input="changeName($event.target.value)"
                />
                <div v-if="fieldError(`participants.${idx}.name`)" class="invalid-tooltip">{{ fieldError(`participants.${idx}.name`) }}</div>
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
                    :class="{ 'is-invalid': fieldError(`participants.${idx}.email`)}"
                    :aria-invalid="fieldError(`participants.${idx}.email`)"
                    @input="changeEmail($event.target.value)"
                />
                <div v-if="fieldError(`participants.${idx}.email`)" class="invalid-tooltip">{{ fieldError(`participants.${idx}.email`) }}</div>
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

<!-- Don't scope the style, multiselect does not handle it -->
<style>
    @import '@vueform/multiselect/themes/default.css';
</style>