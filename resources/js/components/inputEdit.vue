<template>
    <div class="input-group" :data-state="state" :data-previous-state="previousState">
        <div class="input-group-prepend" v-if="updating">
            <i class="input-group-text fas fa-spinner fa-spin"></i>
        </div>
        <div class="input-group-prepend" v-if="state === 'viewUpdated'">
            <i class="input-group-text fas fa-check"></i>
        </div>
        <div class="input-group-prepend" v-if="state === 'viewError'">
            <i class="input-group-text fas fa-exclamation-circle"></i>
        </div>
        <input
            ref="input"
            v-bind="$attrs"
            v-model="newValue"
            class="form-control"
            @input="send('validate')"
            @blur="send('blur')"
            :disabled="view || updating"
        />
        <div class="input-group-append">
            <button
                type="button"
                class="btn btn-primary"
                v-if="state.startsWith('view')"
                @click="send('edit')"
            >
                <i class="fas fa-edit"></i>
            </button>
            <button
                type="button"
                class="btn btn-success"
                v-if="state.startsWith('edit')"
                @click="send('submit')"
                :disabled="updating || isSame || !state.endsWith('Valid')"
            >
                <i class="fas fa-check-circle"></i>
            </button>
            <button
                type="button"
                class="btn btn-danger"
                v-if="state.startsWith('edit')"
                @click="send('cancel')"
                :disabled="updating"
            >
                <i class="fas fa-times-circle"></i>
            </button>
        </div>
    </div>
</template>

<style scoped>
    .input-group > .form-control:not(:first-child), .input-group > .custom-select:not(:first-child) {
        border-left: 0;
        padding-left: 0;
    }
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

<script>
    import Vue from 'vue';

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
                if (this.$el.querySelectorAll('input:invalid').length > 0) {
                    this.send('invalid');
                } else {
                    this.send('valid');
                }
            },
            stateUpdating() {
                this.update(this.newValue)
                    .then(() => {
                        this.value = this.newValue;
                        this.send('success');
                    })
                    .catch(() => {
                        this.send('error');
                    });
            },
            stateUpdated() {
                setTimeout(() => {
                    this.send('timer');
                }, 5000);
            }
        }
    };
</script>
