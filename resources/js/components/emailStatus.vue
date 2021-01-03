<script>
    export default {
        props: {
            delivery_status: {
                type: String,
                required: true
            },
            disabled: {
                type: Boolean,
                default: false
            }
        },
        computed: {
            icon() {
                return {
                    created: "fas fa-spinner",
                    sending: "fas fa-spinner",
                    sent: "fas fa-check",
                    received: "fas fa-check",
                    error: "fas fa-exclamation-triangle"
                }[this.delivery_status];
            }
        }
    }
</script>

<template>
    <div>
        <span>{{ $t(`common.email.status.${delivery_status}`) }} <i :class="[icon, delivery_status]"></i></span>
        <button :disabled="delivery_status === 'created' || disabled" type="button" class="btn btn-outline-secondary" @click="$emit('redo')">
            <i class="fas fa-redo" />
            {{ $t(`common.email.redo`) }}
        </button>
    </div>
</template>

<style scoped>
    .input-group-append {
        display: inline;
        margin-left: 10px;
    }
    .error {
        color: red;
    }
    .sent {
        color: orange;
    }
    .received {
        color: green;
    }
</style>