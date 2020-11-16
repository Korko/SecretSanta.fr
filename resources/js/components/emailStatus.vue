<script>
    export default {
        props: {
            delivery_status: {
                type: String,
                required: true
            }
        },
        computed: {
            icon() {
                return {
                    created: "fas fa-spinner",
                    error: "fas fa-exclamation-triangle",
                    sent: "fas fa-check"
                }[this.delivery_status];
            }
        }
    }
</script>

<template>
    <div>
        <span>{{ $t(`common.email.status.${delivery_status}`) }} <i :class="[icon, delivery_status]"></i></span>
        <button :disabled="delivery_status === 'created'" type="button" class="btn btn-outline-secondary" @click="$emit('redo')">
            <i class="fas fa-redo" />
            Redo
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
        color: green;
    }
</style>