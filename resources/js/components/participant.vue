<script>
    import Multiselect from 'vue-multiselect';

    import Lang from '../partials/lang.js';

    import Vue from 'vue';

    import Vuelidate from 'vuelidate';
    import { required, email, integer, minLength } from 'vuelidate/lib/validators'
    Vue.use(Vuelidate);

    export default {
        components: {
            Multiselect
        },
        props: {
            idx: {
                type: Number,
                required: true
            },
            id: {
                type: String
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
            }
        },
        data: function() {
            return {
                selectedExclusions: this.exclusions.map(exclusion => ({ idx: exclusion, name: this.names[exclusion] })),
                Lang: Lang
            };
        },
        watch: {
            selectedExclusions() {
                this.changeExclusions(this.selectedExclusions.map(exclusion => exclusion.idx));
            }
        },
        computed: {
            otherNames() {
                return Object.keys(this.names).map(idx => parseInt(idx, 10)).filter(idx => idx !== this.idx);
            }
        },
        created: function() {
            if(this.name) this.$v.name.$touch();
            if(this.email) this.$v.email.$touch();
        },
        validations: function() {
            return {
                name: {
                    required: (this.idx < 3),
                    isUnique(value) {
                        // standalone validator ideally should not assume a field is required
                        if (value === '') return true;

                        return Object.values(this.names).filter(name => (name === value)).length === 1;
                    }
                },
                email: {
                    required: (this.name !== ''),
                    email
                }
            };
        },
        methods: {
            formatOptions(ids) {
                return ids.map(idx => ({ idx: idx, name: this.names[idx] }));
            },
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
    <tr :id="'participant_' + idx" class="participant">
        <td class="align-middle">
            <div class="input-group">
                <span class="input-group-prepend counter">
                    <span class="input-group-text"
                        >{{ idx + 1 }}<template v-if="idx === 0"> - Organisateur</template></span
                    >
                </span>
                <input
                    type="text"
                    :name="'participants[' + idx + '][name]'"
                    :placeholder="Lang.get('form.name.placeholder')"
                    :value="name"
                    class="form-control participant-name"
                    @input="changeName($event.target.value)"
                    @blur="$v.name.$touch()"
                    :class="{ 'is-invalid': $v.name.$error }"
                    :aria-invalid="$v.name.$error"
                />
                <div class="invalid-tooltip">Chaque nom doit être unique.</div>
            </div>
        </td>
        <td class="border-left align-middle">
            <div class="input-group">
                <input
                    type="email"
                    :name="'participants[' + idx + '][email]'"
                    :placeholder="Lang.get('form.email.placeholder')"
                    :value="email"
                    class="form-control participant-email"
                    @input="changeEmail($event.target.value)"
                    @blur="$v.email.$touch()"
                    :class="{ 'is-invalid': $v.email.$error }"
                    :aria-invalid="$v.email.$error"
                />
                <div class="invalid-tooltip">Veuillez entrer une adresse valide.</div>
            </div>
        </td>
        <td class="border-right text-left participant-exclusions-wrapper align-middle">
            <multiselect
                :options="formatOptions(otherNames)"
                v-model="selectedExclusions"
                :placeholder="Lang.get('form.exclusions.placeholder')"
                :multiple="true"
                :hide-selected="true"
                :preserve-search="true"
                label="name"
                track-by="idx"
            />
            <select style="display:none" :name="'participants[' + idx + '][exclusions][]'" multiple>
                <option
                    v-for="exclusion in selectedExclusions"
                    :key="exclusion.idx"
                    :value="exclusion.name"
                    selected
                />
            </select>
        </td>
        <td class="participant-remove-wrapper align-middle">
            <button
                type="button"
                class="btn btn-danger participant-remove"
                :disabled="idx < 3 && Object.keys(names).length <= 3"
                @click="$emit('delete')"
            >
                <i class="fas fa-minus" /><span> {{ Lang.get('form.participant.remove') }}</span>
            </button>
        </td>
    </tr>
</template>
