<script>
    export default {
        props: {
            name: {
                type: String,
                required: true,
            },
            type: {
                type: String,
                default: 'text',
            },
            modelValue: {
                type: String,
                default: '',
            },
            errors: {
                type: Array,
                default: () => ([]),
            },
            rules: {
                type: Object,
                default: () => ({}),
            }
        },
        data() {
            return {
                value: this.modelValue,
            };
        },
        emits: ['update:modelValue', 'blur'],
        computed: {
            isInvalid() {
                return this.errors.length > 0;
            },
        },
    };
</script>

<template>
    <div class="input-group">
        <slot />
        <input
            :type="type"
            :name="name"
            v-model="value"
            @input="$emit('update:modelValue', $event.target.value)"
            @blur="$emit('blur')"
            class="form-control"
            :class="{ 'is-invalid': isInvalid }"
            :aria-invalid="isInvalid"
            v-bind="$attrs"
        />
        <div v-if="isInvalid" class="invalid-feedback">
            <ul>
                <li v-for="error in errors" :key="error">
                    {{ error }}
                </li>
            </ul>
        </div>
    </div>
</template>
