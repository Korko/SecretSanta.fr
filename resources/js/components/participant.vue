<script>
    import Multiselect from 'vue-multiselect';

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
            names: {
                type: Object,
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
            $v: {
                type: Object,
                required: true
            }
        },
        computed: {
            otherNames() {
                return Object.values(this.names).filter(name => name !== this.name);
            }
        },
        created: function() {
            if(this.name) this.$v.name.$touch();
            if(this.email) this.$v.email.$touch();
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
            }
        }
    };
</script>

<template>
    <tr class="participant">
        <td class="align-middle">
            <div class="input-group">
                <span class="input-group-prepend counter">
                    <span class="input-group-text"
                        >{{ idx + 1 }}<template v-if="idx === 0"> - {{ $t('form.participant.organizer') }}</template></span
                    >
                </span>
                <input
                    type="text"
                    :name="'participants[' + idx + '][name]'"
                    :placeholder="$t('form.participant.name.placeholder')"
                    :value="name"
                    class="form-control participant-name"
                    :class="{ 'is-invalid': $v.name.$error || fieldError(`participants.${idx}.name`) }"
                    :aria-invalid="$v.name.$error || fieldError(`participants.${idx}.name`)"
                    @input="changeName($event.target.value)"
                    @blur="$v.name.$touch()"
                />
                <div v-if="!$v.name.required" class="invalid-tooltip">{{ $t('validation.custom.randomform.participant.name.required') }}</div>
                <div v-else-if="!$v.name.unique" class="invalid-tooltip">{{ $t('validation.custom.randomform.participant.name.distinct') }}</div>
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
                    :class="{ 'is-invalid': $v.email.$error || fieldError(`participants.${idx}.email`)}"
                    :aria-invalid="$v.email.$error || fieldError(`participants.${idx}.email`)"
                    @input="changeEmail($event.target.value)"
                    @blur="$v.email.$touch()"
                />
                <div v-if="!$v.email.required" class="invalid-tooltip">{{ $t('validation.custom.randomform.participant.email.required') }}</div>
                <div v-else-if="!$v.email.format" class="invalid-tooltip">{{ $t('validation.custom.randomform.participant.email.format') }}</div>
                <div v-else-if="fieldError(`participants.${idx}.email`)" class="invalid-tooltip">{{ fieldError(`participants.${idx}.email`) }}</div>
            </div>
        </td>
        <td class="border-right text-left participant-exclusions-wrapper align-middle">
            <multiselect
                :options="otherNames"
                :value="exclusions"
                :placeholder="$t('form.participant.exclusions.placeholder')"
                :multiple="true"
                :hide-selected="true"
                :preserve-search="true"
                @select="$emit('addExclusion', $event)"
                @remove="$emit('removeExclusion', $event)"
            />
            <select style="display:none" :name="'participants[' + idx + '][exclusions][]'" multiple>
                <option
                    v-for="exclusion in exclusions"
                    :key="exclusion"
                    :value="Object.keys(names).find(idx => names[idx] === exclusion)"
                    selected
                >{{ exclusions }}</option>
            </select>
        </td>
        <td class="participant-remove-wrapper align-middle">
            <button
                type="button"
                class="btn btn-danger participant-remove"
                :disabled="required"
                @click="$emit('delete')"
            >
                <i class="fas fa-minus" /><span> {{ $t('form.participant.remove') }}</span>
            </button>
        </td>
    </tr>
</template>
