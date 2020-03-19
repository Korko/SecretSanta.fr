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
            participants: {
                type: Array,
                required: true
            }
        },
        computed: {
            participantNames: function() {
                var names = [];
                this.participants.forEach(
                    function(participant, idx) {
                        if (participant.name && idx !== this.idx) {
                            names.push({
                                id: participant.id,
                                value: idx,
                                text: participant.name
                            });
                        }
                    }.bind(this)
                );
                return names;
            }
        },
        watch: {
            name: function() {
                this.$emit('input:name', this.name);
            },
            email: function() {
                this.$emit('input:email', this.email);
            },
            exclusions: function() {
                this.$emit('input:exclusions', this.exclusions);
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
                        >@{{ idx + 1
                        }}<template v-if="idx === 0">
                            - Organisateur</template
                        ></span
                    >
                </span>
                <input
                    type="text"
                    :name="'participants[' + idx + '][name]'"
                    :required="idx < 3"
                    :placeholder="lang.get('form.name.placeholder')"
                    :value="name"
                    class="form-control participant-name"
                    @input="$emit('input:name', $event.target.value)"
                />
            </div>
        </td>
        <td class="border-left align-middle">
            <input
                type="email"
                :name="'participants[' + idx + '][email]'"
                :placeholder="lang.get('form.email.placeholder')"
                :value="email"
                class="form-control participant-email"
                :required="idx < 3 || name !== ''"
                @input="$emit('input:email', $event.target.value)"
            />
        </td>
        <td
            class="border-right text-left participant-exclusions-wrapper align-middle"
        >
            <multiselect
                :options="participantNames"
                :value="exclusions"
                track-by="value"
                label="text"
                :placeholder="lang.get('form.exclusions.placeholder')"
                :multiple="true"
                @input="$emit('input:exclusions', $event)"
            />
            <select
                style="display:none"
                :name="'participants[' + idx + '][exclusions][]'"
                multiple
            >
                <option
                    v-for="participantName in participantNames"
                    :key="participantName.value"
                    :value="participantName.value"
                    :selected="
                        exclusions.find(a => a.value === participantName.value)
                    "
                />
            </select>
        </td>
        <td class="participant-remove-wrapper align-middle">
            <button
                type="button"
                class="btn btn-danger participant-remove"
                :disabled="participants.length <= 3"
                @click="$emit('delete')"
            >
                <i class="fas fa-minus" /><span>
                    {{ lang.get('form.participant.remove') }}</span
                >
            </button>
        </td>
    </tr>
</template>
