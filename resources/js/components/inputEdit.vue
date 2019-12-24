<template>
    <form :data-state="state" :data-previousState="previousState">
        <div v-if="state.startsWith('editing')">
            <div class="input-group">
                <input
                    v-bind="$attrs"
                    v-model="newValue"
                    class="form-control"
                    @input="send('validate')"
                    @blur="send('blur')"
                    v-autofocus
                />
                <div class="input-group-append">
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
            </div>
        </div>
        <div v-else>
            <div class="input-group" @click="send('edit')">
                <input :value="newValue" class="form-control disabled" />
            </div>
        </div>
    </form>
</template>

<style scoped>
    .disabled {
        background-color: #ddd;
        color: #999;
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
