<script>
    import Multiselect from 'vue-multiselect';

    import Lang from '../partials/lang.js';

    import Vue from 'vue';

    import Vuelidate from 'vuelidate';
    import { requiredIf, email, integer, minLength } from 'vuelidate/lib/validators'
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
            }
        },
        data: function() {
            return {
                Lang: Lang
            };
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
        validations: function() {
            return {
                name: {
                    required: requiredIf(this.required),
                    isUnique(value) {
                        // standalone validator ideally should not assume a field is required
                        if (value === '') return true;

                        return Object.values(this.names).filter(name => (name === value)).length === 1;
                    }
                },
                email: {
                    required: requiredIf(this.name !== ''),
                    email
                }
            };
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
                :options="otherNames"
                :value="exclusions"
                :placeholder="Lang.get('form.exclusions.placeholder')"
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
                <i class="fas fa-minus" /><span> {{ Lang.get('form.participant.remove') }}</span>
            </button>
        </td>
    </tr>
</template>
