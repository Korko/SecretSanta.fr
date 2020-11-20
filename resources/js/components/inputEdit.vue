<script>
    import Vue from 'vue';

    import Vuelidate from 'vuelidate';
    Vue.use(Vuelidate);

    import $ from 'jquery';
    import alertify from 'alertify.js';

    import store from '../partials/store.js';

    import StateMachine from '../mixins/stateMachine.js';

    export default {
        mixins: [StateMachine],
        inheritAttrs: false,
        props: {
            name: {
                type: String,
                required: true
            },
            value: {
                type: String,
                required: true
            },
            action: {
                type: String,
                required: true
            },
            validation: {
                type: Object,
                default: null
            }
        },
        validations() {
            return {
                newValue: this.validation || {}
            };
        },
        data: function() {
            return {
                ...store,
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
                        submit: 'viewUpdating',
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
                    viewUpdating: {
                        success: 'viewUpdated',
                        error: 'viewError'
                    },
                    viewUpdated: {
                        edit: 'editing',
                        timer: 'view'
                    },
                    viewError: {
                        edit: 'editing',
                        resend: 'viewUpdating'
                    }
                }),
                state: 'view',
                newValue: this.value
            };
        },
        computed: {
            isSame() {
                return this.newValue === this.value;
            },
            view() {
                return this.state.startsWith('view');
            },
            updating() {
                return this.state === 'viewUpdating';
            }
        },
        methods: {
            onBlur() {
                this.$v.$touch();
                this.send('blur');
            },
            onCancel() {
                this.$v.$reset();
                this.send('cancel');
            },
            onInput() {
                this.$v.$error && this.$v.$touch();
                this.send('validate');
            },
            onSubmit() {
                this.$v.$touch();
                this.send('submit');
            },
            submit() {
                var app = this;
                return $.ajax({
                    url: this.action,
                    type: 'POST',
                    data: {
                        _token: this.csrf,
                        key: this.key,
                        [this.name]: this.newValue
                    },
                    success(data, textStatus, jqXHR) {
                        var update = { value: app.newValue };
                        if (jqXHR.responseJSON) {
                            if (jqXHR.responseJSON.message) alertify.success(jqXHR.responseJSON.message);
                            Object.assign(update, jqXHR.responseJSON);
                        }
                        app.$emit('update', update);
                    },
                    error(jqXHR) {
                        if (jqXHR.responseJSON && jqXHR.responseJSON.message)
                            alertify.error(jqXHR.responseJSON.message);
                    }
                });
            },
            stateView() {
                this.newValue = this.value;
            },
            stateEditing() {
                this.$nextTick(() => this.$refs.input.focus());
            },
            stateEditingBlur() {
                if (this.isSame) {
                    this.send('same');
                } else {
                    this.send('different');
                }
            },
            stateEditingValidating() {
                if (this.$v.$invalid || this.$el.querySelectorAll('input:invalid').length > 0) {
                    this.send('invalid');
                } else {
                    this.send('valid');
                }
            },
            stateViewUpdating() {
                this.submit()
                    .then(() => {
                        this.send('success');
                    })
                    .catch(() => {
                        this.send('error');
                    });
            },
            stateViewUpdated() {
                setTimeout(() => {
                    this.send('timer');
                }, 5000);
            }
        }
    };
</script>

<template>
    <form :action="action" method="post" autocomplete="off" @submit.prevent="onSubmit">
        <fieldset :disabled="updating">
            <div class="input-group" :data-state="state" :data-previous-state="previousState">
                <div v-if="updating" class="input-group-prepend">
                    <i class="input-group-text fas fa-spinner fa-spin" />
                </div>
                <div v-if="state === 'viewUpdated'" class="input-group-prepend">
                    <i class="input-group-text fas fa-check" />
                </div>
                <div v-if="state === 'viewError'" class="input-group-prepend">
                    <i class="input-group-text fas fa-exclamation-circle" />
                </div>
                <input
                    ref="input"
                    v-model="newValue"
                    :name="name"
                    v-bind="$attrs"
                    :class="{ 'form-control': true, 'is-invalid': $v.$error }"
                    :disabled="view"
                    :aria-invalid="$v.$error"
                    @input="onInput"
                />
                <slot name="errors" :$v="$v.newValue"></slot>
                <div class="input-group-append">
                    <button
                        v-if="state.startsWith('view')"
                        type="button"
                        class="btn btn-outline-primary"
                        @click="send('edit')"
                    >
                        <i class="fas fa-edit" />
                    </button>
                    <button
                        v-if="state.startsWith('edit')"
                        type="button"
                        class="btn btn-outline-success"
                        :disabled="isSame || !state.endsWith('Valid')"
                        @click="onSubmit"
                    >
                        <i class="fas fa-check-circle" />
                    </button>
                    <button
                        v-if="state.startsWith('edit')"
                        type="button"
                        class="btn btn-outline-danger"
                        @click="onCancel"
                    >
                        <i class="fas fa-times-circle" />
                    </button>
                </div>
            </div>
        </fieldset>
    </form>
</template>

<style scoped>
    .input-group > .input-group-prepend > .input-group-text {
        border-right: 0;
    }

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

    .input-group[data-state='viewUpdated'] .input-group-text {
        color: var(--success);
        background: none;
    }

    .input-group[data-state='viewError'] .input-group-text {
        color: var(--danger);
        background: none;
    }

    .input-group[data-state='viewUpdated']::after {
        width: 100%;
        background-color: var(--success);
    }

    .input-group[data-state='viewError']::after {
        width: 100%;
        background-color: var(--danger);
    }

    input {
        background: none;
        box-shadow: none !important;
        height: 100%;
    }

    .table-hover tbody tr:hover input {
        color: #212529;
    }

    @keyframes check {
        0% {
            stroke-dashoffset: 10;
        }
        100% {
            stroke-dashoffset: 0;
        }
    }

    .fa-check path {
        animation-name: check;
        animation-duration: 2s;
        transition: stroke-dashoffset 0.35s;
        transform-origin: 50% 50%;
    }
</style>
