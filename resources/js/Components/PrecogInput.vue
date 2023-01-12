<script>
    export default {
        inject: ['validate', 'fieldError'],
        props: {
            name: {
                type: String,
                required: true
            },
            value: {
                type: String,
                default: ''
            }
        },
        data() {
            return {
                localValue: this.value
            };
        },
        computed: {
            errors() {
                return this.fieldError(this.name) || [];
            },
            isInvalid() {
                return this.errors.length > 0;
            }
        },
        watch: {
            localValue() {
                this.$emit('input', this.localValue);
            },
        }
    }
</script>

<template>
    <div class="input-group">
        <input
            :name="name"
            :value="localValue"
            class="form-control"
            :class="{ 'is-invalid': isInvalid }"
            :aria-invalid="isInvalid"
            @blur="validate(name, localValue)"
            @input="localValue = $event.target.value"
        />
        <div v-if="isInvalid" class="invalid-tooltip">{{ errors }}</div>
    </div>
</template>
