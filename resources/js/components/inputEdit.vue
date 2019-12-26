<template>
    <form
        class="input-group"
        :data-state="state"
        :data-previous-state="previousState"
    >
        <input
            v-bind="$attrs"
            v-model="newValue"
            class="form-control"
            @click="send('edit')"
            @input="send('validate')"
            @blur="send('blur')"
            v-autofocus
        />
        <div class="input-group-append" v-if="state === 'updating'">
            <button type="button" class="btn btn-secondary">
                <i class="fas fa-spinner"></i>
            </button>
        </div>
        <div
            class="input-group-append"
            v-else-if="state.startsWith('editing') || state === 'error'"
        >
            <button
                type="button"
                class="btn btn-success"
                @click="send('submit')"
                :disabled="isSame || !state.endsWith('Valid')"
            >
                <i class="fas fa-check-circle"></i>
            </button>
            <button
                type="button"
                class="btn btn-danger"
                @click="send('cancel')"
            >
                <i class="fas fa-times-circle"></i>
            </button>
        </div>
    </form>
</template>

<style scoped>
    .input-group::after {
        content: '';
        box-sizing: border-box;
        width: 0;
        height: 2px;

        position: absolute;
        bottom: -4px;
        left: 0;

        will-change: width;
        transition: width 0.285s ease-out;
        z-index: 4;
    }
    .input-group-append {
        z-index: 5;
    }
    .input-group[data-state='updated']::after {
        background-color: #2c642c;
        width: 100%;
    }
    .input-group[data-state='error']::after {
        background-color: #a82824;
        width: 100%;
    }
    input {
        border: 0;
        background: none;
        box-shadow: none !important;
    }
    .table-hover tbody tr:hover input {
        color: #212529;
    }
    @keyframes bg {
        0% {
            background-size: 0 3px, 3px 0, 0 3px, 3px 0;
        }
        25% {
            background-size: 100% 3px, 3px 0, 0 3px, 3px 0;
        }
        50% {
            background-size: 100% 3px, 3px 100%, 0 3px, 3px 0;
        }
        75% {
            background-size: 100% 3px, 3px 100%, 100% 3px, 3px 0;
        }
        100% {
            background-size: 100% 3px, 3px 100%, 100% 3px, 3px 100%;
        }
    }
</style>

<script>
    import Vue from 'vue';
    import autofocus from 'vue-autofocus-directive';
    Vue.directive('autofocus', autofocus);

    import StateMachine from '../mixins/stateMachine.js';

    export default {
        inheritAttrs: false,
        mixins: [StateMachine],
        props: ['value', 'update'],
        data() {
            return {
                states: Object.freeze({
                    view: {
                        edit: 'editing'
                    },
                    editing: {
                        validate: 'editingValidating',
                        cancel: 'view',
                        blur: 'editingBlur'
                    },
                    editingBlur: {
                        same: 'view',
                        different: 'editingValidating'
                    },
                    editingValid: {
                        validate: 'editingValidating',
                        submit: 'updating',
                        cancel: 'view',
                        blur: 'editingBlur'
                    },
                    editingInvalid: {
                        validate: 'editingValidating',
                        cancel: 'view'
                    },
                    editingValidating: {
                        valid: 'editingValid',
                        invalid: 'editingInvalid'
                    },
                    updating: {
                        success: 'updated',
                        error: 'error'
                    },
                    updated: {
                        timer: 'view'
                    },
                    error: {
                        edit: 'editing',
                        resend: 'updating'
                    }
                }),
                state: 'view',
                newValue: this.value
            };
        },
        computed: {
            isSame() {
                return this.newValue === this.value;
            }
        },
        methods: {
            stateView() {
                this.newValue = this.value;
            },
            stateEditingBlur() {
                if (this.isSame) {
                    this.send('same');
                } else {
                    this.send('different');
                }
            },
            stateEditingValidating() {
                if (this.$el.querySelectorAll('input:invalid').length > 0) {
                    this.send('invalid');
                } else {
                    this.send('valid');
                }
            },
            stateUpdating() {
                this.update(this.newValue)
                    .then(() => {
                        this.send('success');
                    })
                    .catch(() => {
                        this.send('error');
                    });
            },
            stateUpdated() {
                this.$emit('value', this.newValue);
                setTimeout(() => {
                    this.send('timer');
                }, 5000);
            }
        }
    };
</script>
